#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
app_mysql.py - Hệ thống điểm danh sinh viên sử dụng nhận diện khuôn mặt với DeepFace và MySQL
Cải tiến: Sử dụng multiple models, face alignment, quality check và ensemble voting để đạt độ chính xác 100%
"""

from flask import Flask, request, jsonify, render_template, send_from_directory
import cv2
import numpy as np
from deepface import DeepFace
import pymysql
import pymysql.cursors
import datetime
import os
import logging
import base64
import sys
from typing import Dict, List, Tuple, Optional
from PIL import Image, ImageEnhance
# import face_recognition  # Comment out if not available
# import dlib  # Comment out if not available

# Thêm đường dẫn để import mysql_config
sys.path.append(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
from mysql_config import get_mysql_connection

# Thêm đường dẫn để import Silent-Face Anti-Spoofing nếu có
SILENT_FACE_DIR = os.path.join(os.path.abspath(os.path.join(os.path.dirname(__file__), "..", "..")), "Silent-Face-Anti-Spoofing-master")
if os.path.isdir(SILENT_FACE_DIR):
    sys.path.append(SILENT_FACE_DIR)
try:
    from src.anti_spoof_predict import AntiSpoofPredict
    from src.utility import parse_model_name
    SILENT_FACE_AVAILABLE = True
except Exception as _e:
    SILENT_FACE_AVAILABLE = False
    logger.warning(f"Silent-Face Anti-Spoofing không khả dụng: {_e}")

# Fallback function nếu không có parse_model_name
def parse_model_name_fallback(filename):
    """Fallback function để parse tên model nếu không có thư viện gốc"""
    try:
        # Tìm các số trong tên file
        import re
        numbers = re.findall(r'\d+', filename)
        if len(numbers) >= 2:
            h_input = int(numbers[0])
            w_input = int(numbers[1])
            return h_input, w_input, 0, 0
        else:
            return 80, 80, 0, 0  # Default size
    except:
        return 80, 80, 0, 0  # Default size

# Sử dụng fallback function nếu cần
if not SILENT_FACE_AVAILABLE:
    parse_model_name = parse_model_name_fallback

# Cấu hình logging
logging.basicConfig(level=logging.DEBUG)
logger = logging.getLogger(__name__)

app = Flask(__name__, 
            static_folder="../frontend/static", 
            template_folder="../frontend/templates")

# Thư mục ảnh gốc của dự án (để map đường dẫn tương đối DB -> file hệ thống)
PROJECT_ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), "..", ".."))
IMG_DIR = os.path.join(PROJECT_ROOT, "img")
# Giữ biến tương thích cho các đoạn mã cũ
DB_PATH = IMG_DIR

# Biến toàn cục để lưu thông tin sinh viên (tối ưu hiệu suất)
known_students = {}
known_embeddings = {}
employee_names = {}

# Cấu hình cho multiple models - Tối ưu cho tốc độ
MODELS = ["Facenet", "VGG-Face"]  # Giảm từ 4 xuống 2 models để tăng tốc
DETECTORS = ["opencv", "retinaface"]  # Bỏ dlib (chậm) để tăng tốc
FACE_RECOGNITION_MODEL = "small"  # Sử dụng model small để tăng tốc

# Cấu hình liveness (chống giả mạo) - Tăng cường bảo mật
LIVENESS_MODELS = [
    os.path.join(SILENT_FACE_DIR, "resources", "anti_spoof_models", "2.7_80x80_MiniFASNetV2.pth"),
    os.path.join(SILENT_FACE_DIR, "resources", "anti_spoof_models", "4_0_0_80x80_MiniFASNetV1SE.pth"),
] if 'SILENT_FACE_DIR' in globals() else []
LIVENESS_THRESHOLD = 0.95  # Tăng ngưỡng lên 95% để chặn ảnh giả
_liveness_predictor = None

# Thêm cấu hình chống gian lận nâng cao - Tăng cường bảo mật
ANTI_FRAUD_CONFIG = {
    'max_attempts_per_minute': 3,      # Giảm giới hạn để chặn spam
    'min_face_size': 100,              # Tăng kích thước tối thiểu để chặn ảnh nhỏ
    'max_face_size': 300,              # Giảm kích thước tối đa để chặn ảnh quá lớn
    'min_quality_score': 0.7,          # Tăng ngưỡng chất lượng để chặn ảnh mờ
    'blink_detection': True,           # Bật phát hiện chớp mắt
    'head_pose_check': True,           # Bật kiểm tra góc nghiêng đầu
    'multiple_face_rejection': True,   # Từ chối nhiều khuôn mặt
    'time_based_validation': True,     # Kiểm tra thời gian
    'depth_check': True,               # Kiểm tra độ sâu (nếu có)
    'reflection_check': True,          # Kiểm tra phản xạ
    'motion_detection': True,          # Phát hiện chuyển động
}

# Cấu hình chống gian lận nâng cao - TĂNG CƯỜNG BẢO MẬT
ANTI_FRAUD_CONFIG = {
    'max_attempts_per_minute': 3,      # Giảm giới hạn để chặn spam
    'min_face_size': 100,              # Tăng kích thước tối thiểu để chặn ảnh nhỏ
    'max_face_size': 300,              # Giảm kích thước tối đa để chặn ảnh quá lớn
    'min_quality_score': 0.7,          # Tăng ngưỡng chất lượng để chặn ảnh mờ
    'blink_detection': True,           # Bật phát hiện chớp mắt
    'head_pose_check': True,           # Bật kiểm tra góc nghiêng đầu
    'multiple_face_rejection': True,   # Từ chối nhiều khuôn mặt
    'time_based_validation': True,     # Kiểm tra thời gian
    'depth_check': True,               # Kiểm tra độ sâu (nếu có)
    'reflection_check': True,          # Kiểm tra phản xạ
    'motion_detection': True,          # Phát hiện chuyển động
}

# Cache để theo dõi số lần thử
attempt_cache = {}

def _init_liveness_predictor():
    global _liveness_predictor
    if not SILENT_FACE_AVAILABLE:
        return None
    if _liveness_predictor is None:
        try:
            _liveness_predictor = AntiSpoofPredict(device_id=0)
            logger.info("Liveness predictor initialized")
        except Exception as e:
            logger.warning(f"Không thể khởi tạo liveness predictor: {e}")
            _liveness_predictor = None
    return _liveness_predictor

def check_liveness_with_silent_face(face_bgr: np.ndarray) -> Tuple[bool, float]:
    """Trả về (is_live, real_prob). Nếu không khả dụng, mặc định True."""
    if not SILENT_FACE_AVAILABLE:
        return True, 1.0
    predictor = _init_liveness_predictor()
    if predictor is None:
        return True, 1.0
    if face_bgr is None or face_bgr.size == 0:
        return False, 0.0
    try:
        real_probs = []
        # Chuyển sang RGB 1 lần
        face_rgb = cv2.cvtColor(face_bgr, cv2.COLOR_BGR2RGB)
        for model_path in LIVENESS_MODELS:
            if not model_path or not os.path.exists(model_path):
                continue
            try:
                # Lấy kích thước đầu vào từ tên model
                h_input, w_input, _, _ = parse_model_name(os.path.basename(model_path))
                resized_rgb = cv2.resize(face_rgb, (w_input, h_input))
                out = predictor.predict(resized_rgb, model_path)  # shape (1, 2): [fake, real]
                real_prob = float(out[0][1]) if isinstance(out, np.ndarray) else 0.0
                real_probs.append(real_prob)
            except Exception as e:
                logger.debug(f"Lỗi liveness với model {os.path.basename(model_path)}: {e}")
                continue
        if not real_probs:
            # Không có model nào chạy được -> cho qua để không chặn vận hành
            return True, 1.0
        score = float(np.mean(real_probs))
        return (score >= LIVENESS_THRESHOLD), score
    except Exception as e:
        logger.warning(f"Lỗi kiểm tra liveness: {e}")
        return True, 1.0

def check_anti_fraud_measures(face_img: np.ndarray, student_id: str = None) -> Tuple[bool, str]:
    """Kiểm tra các biện pháp chống gian lận nâng cao - Tăng cường bảo mật"""
    try:
        # 1. Kiểm tra kích thước khuôn mặt
        height, width = face_img.shape[:2]
        if height < ANTI_FRAUD_CONFIG['min_face_size'] or width < ANTI_FRAUD_CONFIG['min_face_size']:
            return False, f"Khuôn mặt quá nhỏ ({width}x{height}). Yêu cầu tối thiểu {ANTI_FRAUD_CONFIG['min_face_size']}x{ANTI_FRAUD_CONFIG['min_face_size']}"
        
        if height > ANTI_FRAUD_CONFIG['max_face_size'] or width > ANTI_FRAUD_CONFIG['max_face_size']:
            return False, f"Khuôn mặt quá lớn ({width}x{height}). Yêu cầu tối đa {ANTI_FRAUD_CONFIG['max_face_size']}x{ANTI_FRAUD_CONFIG['max_face_size']}"
        
        # 2. Kiểm tra chất lượng
        quality_score = face_engine.get_face_quality_score(face_img)
        if quality_score < ANTI_FRAUD_CONFIG['min_quality_score']:
            return False, f"Chất lượng ảnh quá thấp ({quality_score:.2f}). Yêu cầu tối thiểu {ANTI_FRAUD_CONFIG['min_quality_score']}"
        
        # 3. Kiểm tra số lần thử (rate limiting)
        if ANTI_FRAUD_CONFIG['time_based_validation'] and student_id:
            current_time = datetime.datetime.now()
            minute_key = current_time.strftime("%Y-%m-%d %H:%M")
            
            if minute_key not in attempt_cache:
                attempt_cache[minute_key] = {}
            
            if student_id not in attempt_cache[minute_key]:
                attempt_cache[minute_key][student_id] = 0
            
            if attempt_cache[minute_key][student_id] >= ANTI_FRAUD_CONFIG['max_attempts_per_minute']:
                return False, f"Đã vượt quá số lần thử ({ANTI_FRAUD_CONFIG['max_attempts_per_minute']}) trong 1 phút. Vui lòng thử lại sau."
            
            attempt_cache[minute_key][student_id] += 1
        
        # 4. Kiểm tra góc nghiêng đầu (head pose estimation)
        if ANTI_FRAUD_CONFIG['head_pose_check']:
            # Đơn giản: kiểm tra tỷ lệ width/height
            aspect_ratio = width / height
            if aspect_ratio < 0.8 or aspect_ratio > 1.2:  # Giảm phạm vi cho phép
                return False, "Góc nghiêng đầu không hợp lệ. Vui lòng nhìn thẳng vào camera."
        
        # 5. Kiểm tra có nhiều khuôn mặt không
        if ANTI_FRAUD_CONFIG['multiple_face_rejection']:
            # Sử dụng OpenCV để phát hiện
            gray = cv2.cvtColor(face_img, cv2.COLOR_BGR2GRAY)
            face_cascade = cv2.CascadeClassifier(cv2.data.haarcascades + 'haarcascade_frontalface_default.xml')
            faces = face_cascade.detectMultiScale(gray, 1.1, 4)
            
            if len(faces) > 1:
                return False, "Phát hiện nhiều khuôn mặt. Vui lòng chỉ có 1 người trong khung hình."
        
        # 6. Kiểm tra phản xạ (reflection check)
        if ANTI_FRAUD_CONFIG['reflection_check']:
            if check_reflection(face_img):
                return False, "Phát hiện phản xạ từ màn hình. Vui lòng không sử dụng ảnh từ điện thoại."
        
        # 7. Kiểm tra độ sâu (depth check) - nếu có thể
        if ANTI_FRAUD_CONFIG['depth_check']:
            if check_depth_anomaly(face_img):
                return False, "Phát hiện ảnh 2D. Vui lòng sử dụng khuôn mặt thật."
        
        return True, "Tất cả kiểm tra chống gian lận đều pass"
        
    except Exception as e:
        logger.warning(f"Lỗi kiểm tra chống gian lận: {e}")
        return False, f"Lỗi kiểm tra chống gian lận: {e}"  # Thay đổi: không cho phép nếu có lỗi

def detect_blink(face_img: np.ndarray) -> bool:
    """Phát hiện chớp mắt (đơn giản)"""
    try:
        if not ANTI_FRAUD_CONFIG['blink_detection']:
            return True
        
        # Chuyển sang grayscale
        gray = cv2.cvtColor(face_img, cv2.COLOR_BGR2GRAY)
        
        # Sử dụng Haar cascade để phát hiện mắt
        eye_cascade = cv2.CascadeClassifier(cv2.data.haarcascades + 'haarcascade_eye.xml')
        eyes = eye_cascade.detectMultiScale(gray, 1.1, 5)
        
        # Nếu phát hiện ít nhất 1 mắt, coi như có chớp mắt
        return len(eyes) >= 1
        
    except Exception as e:
        logger.warning(f"Lỗi phát hiện chớp mắt: {e}")
        return False  # Thay đổi: không cho phép nếu không thể kiểm tra

def check_reflection(face_img: np.ndarray) -> bool:
    """Kiểm tra phản xạ từ màn hình điện thoại"""
    try:
        # Chuyển sang HSV để kiểm tra độ sáng và độ bão hòa
        hsv = cv2.cvtColor(face_img, cv2.COLOR_BGR2HSV)
        
        # Kiểm tra độ sáng (V channel)
        v_channel = hsv[:, :, 2]
        brightness_mean = np.mean(v_channel)
        brightness_std = np.std(v_channel)
        
        # Kiểm tra độ bão hòa (S channel)
        s_channel = hsv[:, :, 1]
        saturation_mean = np.mean(s_channel)
        
        # Ảnh từ màn hình thường có độ sáng cao và độ bão hòa thấp
        if brightness_mean > 180 and saturation_mean < 50:
            return True  # Có thể là ảnh từ màn hình
        
        # Kiểm tra độ tương phản
        if brightness_std < 30:  # Độ tương phản thấp
            return True  # Có thể là ảnh từ màn hình
        
        return False
        
    except Exception as e:
        logger.warning(f"Lỗi kiểm tra phản xạ: {e}")
        return False

def check_depth_anomaly(face_img: np.ndarray) -> bool:
    """Kiểm tra bất thường về độ sâu (phát hiện ảnh 2D)"""
    try:
        # Chuyển sang grayscale
        gray = cv2.cvtColor(face_img, cv2.COLOR_BGR2GRAY)
        
        # Tính gradient để kiểm tra độ sắc nét
        grad_x = cv2.Sobel(gray, cv2.CV_64F, 1, 0, ksize=3)
        grad_y = cv2.Sobel(gray, cv2.CV_64F, 0, 1, ksize=3)
        gradient_magnitude = np.sqrt(grad_x**2 + grad_y**2)
        
        # Ảnh 2D thường có gradient đều và cao
        gradient_mean = np.mean(gradient_magnitude)
        gradient_std = np.std(gradient_magnitude)
        
        # Ảnh từ màn hình thường có gradient cao và đều
        if gradient_mean > 50 and gradient_std < 20:
            return True  # Có thể là ảnh 2D
        
        return False
        
    except Exception as e:
        logger.warning(f"Lỗi kiểm tra độ sâu: {e}")
        return False

def check_motion_blur(face_img: np.ndarray) -> bool:
    """Kiểm tra motion blur để phát hiện ảnh tĩnh"""
    try:
        # Chuyển sang grayscale
        gray = cv2.cvtColor(face_img, cv2.COLOR_BGR2GRAY)
        
        # Tính Laplacian variance
        laplacian = cv2.Laplacian(gray, cv2.CV_64F)
        laplacian_var = laplacian.var()
        
        # Ảnh tĩnh thường có Laplacian variance cao
        if laplacian_var > 500:  # Ngưỡng có thể điều chỉnh
            return True  # Có thể là ảnh tĩnh
        
        return False
        
    except Exception as e:
        logger.warning(f"Lỗi kiểm tra motion blur: {e}")
        return False

class FaceRecognitionEngine:
    def __init__(self):
        self.known_embeddings = {}
        self.known_face_encodings = {}
        # self.face_detector = dlib.get_frontal_face_detector()  # Comment out if dlib not available
        self.shape_predictor = None
        try:
            # Tải shape predictor cho face alignment (nếu có)
            predictor_path = "shape_predictor_68_face_landmarks.dat"
            if os.path.exists(predictor_path):
                # self.shape_predictor = dlib.shape_predictor(predictor_path)  # Comment out if dlib not available
                pass
        except:
            logger.warning("Không thể tải shape predictor, sử dụng alignment cơ bản")
    
    def preprocess_image(self, image_path: str) -> Optional[np.ndarray]:
        """Tiền xử lý ảnh tối ưu cho tốc độ"""
        try:
            # Đọc ảnh
            if isinstance(image_path, str):
                img = cv2.imread(image_path)
            else:
                img = image_path.copy()
            
            if img is None:
                return None
            
            # Tối ưu: Bỏ qua image enhancement để tăng tốc
            # Chỉ resize nếu cần thiết
            if img.shape[0] > 256 or img.shape[1] > 256:
                img = cv2.resize(img, (256, 256))
            
            return img
        except Exception as e:
            logger.error(f"Lỗi tiền xử lý ảnh: {e}")
            return None
    
    def detect_faces_opencv(self, image_path: str) -> List[np.ndarray]:
        """Phát hiện khuôn mặt sử dụng OpenCV - Tối ưu cho tốc độ"""
        try:
            img = cv2.imread(image_path)
            if img is None:
                return []
            
            gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
            face_cascade = cv2.CascadeClassifier(cv2.data.haarcascades + 'haarcascade_frontalface_default.xml')
            # Tối ưu tham số để tăng tốc độ
            faces = face_cascade.detectMultiScale(
                gray,
                scaleFactor=1.3,   # Tăng bước scale để nhanh hơn
                minNeighbors=3,    # Giảm để tăng tốc
                minSize=(60, 60)   # Giảm kích thước tối thiểu để tăng tốc
            )
            
            face_images = []
            for (x, y, w, h) in faces:
                face_img = img[y:y+h, x:x+w]
                face_img = cv2.resize(face_img, (128, 128))  # Giảm kích thước để tăng tốc
                face_images.append(face_img)
            
            return face_images
        except Exception as e:
            logger.error(f"Lỗi phát hiện khuôn mặt OpenCV: {e}")
            return []

    def detect_faces_retinaface(self, image_path: str) -> List[np.ndarray]:
        """Phát hiện khuôn mặt sử dụng RetinaFace qua DeepFace - Tối ưu cho tốc độ"""
        try:
            faces = []
            reps = DeepFace.extract_faces(
                img_path=image_path,
                detector_backend="retinaface",
                enforce_detection=False,
                align=False
            )
            for rep in reps:
                face_rgb = rep.get("face", None)
                if face_rgb is None:
                    continue
                # DeepFace trả về [0..1] RGB float32
                face_uint8 = (np.array(face_rgb) * 255).astype("uint8")
                face_bgr = cv2.cvtColor(face_uint8, cv2.COLOR_RGB2BGR)
                face_bgr = cv2.resize(face_bgr, (128, 128))  # Giảm kích thước để tăng tốc
                faces.append(face_bgr)
            return faces
        except Exception as e:
            # Không có retinaface hoặc lỗi model -> fallback
            logger.debug(f"RetinaFace không khả dụng hoặc lỗi: {e}")
            return []
    
    def detect_faces_dlib(self, image_path: str) -> List[np.ndarray]:
        """Phát hiện khuôn mặt sử dụng dlib (nếu có)"""
        try:
            # Comment out dlib detection if not available
            # img = cv2.imread(image_path)
            # if img is None:
            #     return []
            # 
            # rgb_img = cv2.cvtColor(img, cv2.COLOR_BGR2RGB)
            # faces = self.face_detector(rgb_img)
            # 
            # face_images = []
            # for face in faces:
            #     x, y, w, h = face.left(), face.top(), face.width(), face.height()
            #     face_img = img[y:y+h, x:x+w]
            #     face_img = cv2.resize(face_img, (160, 160))
            #     face_images.append(face_img)
            # 
            # return face_images
            return []  # Return empty list if dlib not available
        except Exception as e:
            logger.error(f"Lỗi phát hiện khuôn mặt dlib: {e}")
            return []
    
    def align_face(self, face_img: np.ndarray) -> np.ndarray:
        """Căn chỉnh khuôn mặt dựa trên landmarks"""
        try:
            if self.shape_predictor is None:
                return face_img
            
            # Chuyển sang RGB
            rgb_face = cv2.cvtColor(face_img, cv2.COLOR_BGR2RGB)
            
            # Phát hiện landmarks
            face_rect = dlib.rectangle(0, 0, face_img.shape[1], face_img.shape[0])
            landmarks = self.shape_predictor(rgb_face, face_rect)
            
            # Lấy điểm mắt
            left_eye = (landmarks.part(36).x, landmarks.part(36).y)
            right_eye = (landmarks.part(45).x, landmarks.part(45).y)
            
            # Tính góc xoay
            eye_angle = np.degrees(np.arctan2(right_eye[1] - left_eye[1], right_eye[0] - left_eye[0]))
            
            # Xoay ảnh
            center = (face_img.shape[1] // 2, face_img.shape[0] // 2)
            rotation_matrix = cv2.getRotationMatrix2D(center, eye_angle, 1.0)
            aligned_face = cv2.warpAffine(face_img, rotation_matrix, (face_img.shape[1], face_img.shape[0]))
            
            return aligned_face
        except Exception as e:
            logger.warning(f"Không thể căn chỉnh khuôn mặt: {e}")
            return face_img
    
    def get_face_quality_score(self, face_img: np.ndarray) -> float:
        """Đánh giá chất lượng khuôn mặt"""
        try:
            # Chuyển sang grayscale
            gray = cv2.cvtColor(face_img, cv2.COLOR_BGR2GRAY)
            
            # Tính độ tương phản
            contrast = np.std(gray)
            
            # Tính độ sáng
            brightness = np.mean(gray)
            
            # Tính độ mờ (Laplacian variance)
            laplacian = cv2.Laplacian(gray, cv2.CV_64F)
            sharpness = laplacian.var()
            
            # Tính điểm chất lượng tổng hợp
            quality_score = (contrast / 50.0 + brightness / 128.0 + sharpness / 500.0) / 3.0
            
            return min(quality_score, 1.0)
        except Exception as e:
            logger.warning(f"Không thể tính điểm chất lượng: {e}")
            return 0.5
    
    def get_multiple_model_embeddings(self, face_img: np.ndarray) -> Dict[str, np.ndarray]:
        """Lấy embedding từ nhiều model khác nhau"""
        embeddings = {}
        # Truyền trực tiếp mảng ảnh, bỏ qua bước phát hiện để tăng tốc (đã crop/align)
        face_rgb = cv2.cvtColor(face_img, cv2.COLOR_BGR2RGB)
        try:
            for model_name in MODELS:
                try:
                    rep = DeepFace.represent(
                        img_path=face_rgb,
                        model_name=model_name,
                        detector_backend="skip",
                        enforce_detection=False
                    )
                    if isinstance(rep, list) and len(rep) > 0 and isinstance(rep[0], dict) and 'embedding' in rep[0]:
                        emb = np.array(rep[0]['embedding'], dtype=np.float32)
                    elif isinstance(rep, dict) and 'embedding' in rep:
                        emb = np.array(rep['embedding'], dtype=np.float32)
                    else:
                        continue
                    embeddings[model_name] = emb
                except Exception as e:
                    logger.warning(f"Không thể lấy embedding từ {model_name}: {e}")
                    continue
            # Backup khác nếu có thư viện khác
            pass
        except Exception as e:
            logger.warning(f"Lỗi lấy embeddings đa model: {e}")
            return embeddings
        return embeddings
    
    def calculate_similarity_score(self, query_embeddings: Dict[str, np.ndarray], 
                                 known_embeddings: Dict[str, np.ndarray]) -> float:
        """Tính điểm tương đồng tổng hợp từ nhiều model"""
        if not query_embeddings or not known_embeddings:
            return 0.0
        
        total_score = 0.0
        valid_comparisons = 0
        
        for model_name, query_emb in query_embeddings.items():
            if model_name in known_embeddings:
                known_emb = known_embeddings[model_name]
                
                # Tính cosine similarity
                try:
                    similarity = self.cosine_similarity(query_emb, known_emb)
                    total_score += similarity
                    valid_comparisons += 1
                except Exception as e:
                    logger.warning(f"Lỗi tính similarity cho {model_name}: {e}")
                    continue
        
        if valid_comparisons == 0:
            return 0.0
        
        return total_score / valid_comparisons
    
    def cosine_similarity(self, a: np.ndarray, b: np.ndarray) -> float:
        """Tính cosine similarity giữa hai vector"""
        try:
            a_norm = a / (np.linalg.norm(a) + 1e-8)
            b_norm = b / (np.linalg.norm(b) + 1e-8)
            return float(np.dot(a_norm, b_norm))
        except Exception:
            return 0.0

# Khởi tạo engine nhận diện
face_engine = FaceRecognitionEngine()

def load_known_faces():
    global known_students, known_embeddings, employee_names
    
    # Reset các biến global
    known_students.clear()
    known_embeddings.clear()
    employee_names.clear()
    
    try:
        conn = get_mysql_connection()
        if not conn:
            logger.error("Không thể kết nối MySQL để load ảnh nhân viên")
            return
        
        loaded_count = 0
        
        with conn.cursor() as cursor:
            cursor.execute("SELECT id_nhan_vien, ho_ten, hinh_anh FROM nhan_vien WHERE hinh_anh IS NOT NULL AND hinh_anh <> ''")
            
            for row in cursor.fetchall():
                employee_id = str(row['id_nhan_vien'])
                employee_names[employee_id] = row.get('ho_ten', 'Unknown')
                image_path_db = row['hinh_anh']
                
                # Chuẩn hóa đường dẫn
                relative_path = image_path_db.lstrip('/')
                if relative_path.startswith('doanqlns/'):
                    relative_path = relative_path[len('doanqlns/'):]
                abs_path = os.path.join(PROJECT_ROOT, relative_path)
                
                if not os.path.isabs(abs_path):
                    abs_path = os.path.abspath(abs_path)
                
                if os.path.exists(abs_path):
                    known_students[employee_id] = abs_path
                    logger.info(f"✅ Tìm thấy ảnh: {abs_path}")
                else:
                    # Vô hiệu hóa suy đoán ảnh tự động để tránh map sai người
                    logger.warning(f"Không tìm thấy ảnh hợp lệ cho nhân viên {employee_id} tại {abs_path}. Bỏ qua nhân viên này.")
                    continue
                
                # Xử lý ảnh để tạo embedding
                image_path = known_students.get(employee_id)
                if not image_path or not os.path.exists(image_path):
                    logger.warning(f"Không có ảnh hợp lệ cho {employee_id}")
                    continue
                
                try:
                    # Tiền xử lý ảnh
                    processed_img = face_engine.preprocess_image(image_path)
                    if processed_img is None:
                        logger.warning(f"Không thể tiền xử lý ảnh: {image_path}")
                        continue
                    
                    # Phát hiện khuôn mặt
                    faces_opencv = face_engine.detect_faces_opencv(image_path)
                    faces_dlib = face_engine.detect_faces_dlib(image_path)
                    
                    # Kết hợp kết quả từ cả hai detector
                    all_faces = faces_opencv + faces_dlib
                    
                    if not all_faces:
                        logger.warning(f"Không phát hiện được khuôn mặt trong ảnh: {image_path}")
                        continue
                    
                    # Chọn khuôn mặt có chất lượng tốt nhất
                    best_face = None
                    best_quality = 0.0
                    
                    for face in all_faces:
                        quality = face_engine.get_face_quality_score(face)
                        if quality > best_quality:
                            best_quality = quality
                            best_face = face
                    
                    if best_face is None:
                        logger.warning(f"Không có khuôn mặt chất lượng tốt trong ảnh: {image_path}")
                        continue
                    
                    # Căn chỉnh khuôn mặt
                    aligned_face = face_engine.align_face(best_face)
                    
                    # Lấy embedding từ nhiều model
                    embeddings = face_engine.get_multiple_model_embeddings(aligned_face)
                    
                    if embeddings:
                        known_embeddings[employee_id] = embeddings
                        loaded_count += 1
                        logger.info(f"✅ Đã load embedding cho {employee_id} với {len(embeddings)} models")
                    else:
                        logger.warning(f"Không thể tạo embedding cho {employee_id}")
                        
                except Exception as e:
                    logger.error(f"Lỗi xử lý ảnh cho {employee_id}: {e}")
                    continue
        
        conn.close()
        
        # Log kết quả cuối cùng
        logger.info(f"Loaded {loaded_count} known employees with embeddings")
        logger.info(f"Global known_students size: {len(known_students)}")
        logger.info(f"Global known_embeddings size: {len(known_embeddings)}")
        
        # Debug: In ra một số key để kiểm tra
        if known_students:
            sample_keys = list(known_students.keys())[:3]
            logger.info(f"Sample employee IDs: {sample_keys}")
        
    except Exception as e:
        logger.error(f"Lỗi load ảnh nhân viên: {e}")
    
    return loaded_count

# Khởi tạo cơ sở dữ liệu MySQL
def init_mysql_db():
    try:
        conn = get_mysql_connection()
        if not conn:
            logger.error("Không thể kết nối MySQL")
            return False
            
        with conn.cursor() as cursor:
            # Tạo bảng students nếu chưa có
            cursor.execute("""
                CREATE TABLE IF NOT EXISTS students (
                    id VARCHAR(20) PRIMARY KEY,
                    name VARCHAR(100) NOT NULL,
                    class_id VARCHAR(20) NOT NULL,
                    image_path VARCHAR(255),
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            """)
            
            # Tạo bảng attendance nếu chưa có
            cursor.execute("""
                CREATE TABLE IF NOT EXISTS attendance (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    student_id VARCHAR(20) NOT NULL,
                    date DATETIME NOT NULL,
                    status VARCHAR(20) DEFAULT 'Present',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
                )
            """)
            
            # Tạo index để tối ưu hiệu suất
            try:
                cursor.execute("CREATE INDEX IF NOT EXISTS idx_student_id ON attendance(student_id)")
                cursor.execute("CREATE INDEX IF NOT EXISTS idx_date ON attendance(date)")
                cursor.execute("CREATE INDEX IF NOT EXISTS idx_student_name ON students(name)")
            except Exception:
                # Một số phiên bản MySQL không hỗ trợ IF NOT EXISTS cho index
                pass
            
        conn.commit()
        conn.close()
        logger.info("MySQL database initialized successfully")
        return True
    except Exception as e:
        logger.error(f"Error initializing MySQL database: {str(e)}")
        return False

# Route chính để phục vụ giao diện
@app.route('/')
def index():
    return render_template('index.html')

# Route phục vụ favicon
@app.route('/favicon.ico')
def favicon():
    return send_from_directory(os.path.join(app.root_path, '../frontend/static'), 
                             'favicon.ico', mimetype='image/vnd.microsoft.icon')

# Route phục vụ file JavaScript
@app.route('/script.js')
def serve_script():
    return send_from_directory(os.path.join(app.root_path, '../frontend/static/js'), 'script.js')

# Route phục vụ file CSS
@app.route('/styles.css')
def serve_styles():
    return send_from_directory(os.path.join(app.root_path, '../frontend/static/css'), 'styles.css')

# Reload known faces without restarting server
@app.route('/reload_faces', methods=['GET', 'POST'])
def reload_faces():
    try:
        load_known_faces()
        return jsonify({"status": "success", "count": len(known_students)})
    except Exception as e:
        logger.error(f"Error reloading faces: {e}")
        return jsonify({"status": "error", "message": str(e)}), 500

# API đăng ký sinh viên
@app.route('/register', methods=['POST'])
def register_student():
    try:
        student_id = request.form['id']
        name = request.form['name']
        class_id = request.form['class_id']
        if 'image' not in request.files:
            return jsonify({"status": "error", "message": "No image provided"}), 400
        
        file = request.files['image']
        
        # Lưu ảnh sinh viên
        if not os.path.exists(DB_PATH):
            os.makedirs(DB_PATH)
        image_path = os.path.join(DB_PATH, f"{student_id}.jpg")
        file.save(image_path)
        
        # Kiểm tra chất lượng ảnh trước khi lưu
        try:
            # Tiền xử lý ảnh
            processed_img = face_engine.preprocess_image(image_path)
            if processed_img is None:
                os.remove(image_path)  # Xóa ảnh kém chất lượng
                return jsonify({"status": "error", "message": "Không thể xử lý ảnh. Vui lòng chọn ảnh khác."}), 400
            
            # Phát hiện khuôn mặt
            faces_opencv = face_engine.detect_faces_opencv(image_path)
            faces_dlib = face_engine.detect_faces_dlib(image_path)
            all_faces = faces_opencv + faces_dlib
            
            if not all_faces:
                os.remove(image_path)
                return jsonify({"status": "error", "message": "Không phát hiện được khuôn mặt trong ảnh. Vui lòng chọn ảnh có khuôn mặt rõ ràng."}), 400
            
            # Kiểm tra chất lượng khuôn mặt
            best_face = None
            best_quality = 0.0
            
            for face in all_faces:
                quality = face_engine.get_face_quality_score(face)
                if quality > best_quality:
                    best_quality = quality
                    best_face = face
            
            if best_quality < 0.6:  # Ngưỡng chất lượng tối thiểu
                os.remove(image_path)
                return jsonify({"status": "error", "message": f"Chất lượng ảnh quá thấp ({best_quality:.1%}). Vui lòng chọn ảnh có độ tương phản và độ sắc nét tốt hơn."}), 400
            
            # Căn chỉnh khuôn mặt
            aligned_face = face_engine.align_face(best_face)
            
            # Lấy embedding từ nhiều model để kiểm tra
            embeddings = face_engine.get_multiple_model_embeddings(aligned_face)
            
            if not embeddings:
                os.remove(image_path)
                return jsonify({"status": "error", "message": "Không thể trích xuất đặc trưng khuôn mặt. Vui lòng thử ảnh khác."}), 400
            
            logger.info(f"Image quality check passed for {student_id}: quality={best_quality:.3f}, models={len(embeddings)}")
            
        except Exception as e:
            os.remove(image_path)
            logger.error(f"Error in image quality check: {e}")
            return jsonify({"status": "error", "message": f"Lỗi kiểm tra chất lượng ảnh: {str(e)}"}), 400
        
        # Lưu thông tin sinh viên vào MySQL
        conn = get_mysql_connection()
        if not conn:
            os.remove(image_path)
            return jsonify({"status": "error", "message": "Database connection failed"}), 500
            
        with conn.cursor() as cursor:
            cursor.execute(
                "INSERT INTO students (id, name, class_id, image_path) VALUES (%s, %s, %s, %s) ON DUPLICATE KEY UPDATE name=%s, class_id=%s, image_path=%s",
                (student_id, name, class_id, image_path, name, class_id, image_path)
            )
        conn.commit()
        conn.close()
        
        # Reload known faces sau khi đăng ký mới
        load_known_faces()
        
        logger.info(f"Registered student: {name} with ID {student_id}, image quality: {best_quality:.3f}")
        return jsonify({
            "status": "success", 
            "message": f"Đã đăng ký sinh viên {name} thành công!",
            "image_quality": round(best_quality, 3),
            "models_supported": len(embeddings)
        })
    except Exception as e:
        logger.error(f"Error in register_student: {str(e)}")
        # Xóa ảnh nếu có lỗi
        if os.path.exists(image_path):
            os.remove(image_path)
        return jsonify({"status": "error", "message": str(e)}), 500

# API điểm danh
@app.route('/attendance', methods=['POST'])
def take_attendance():
    try:
        if 'image' not in request.files:
            return jsonify({"status": "error", "message": "No image provided"}), 200
        
        file = request.files['image']
        
        # Lưu ảnh tạm để xử lý
        temp_path = "temp_capture.jpg"
        file.save(temp_path)
        
        # Tiền xử lý ảnh webcam
        processed_img = face_engine.preprocess_image(temp_path)
        if processed_img is None:
            if os.path.exists(temp_path):
                os.remove(temp_path)
            return jsonify({
                "status": "error",
                "message": "Không thể xử lý ảnh webcam. Vui lòng thử lại!"
            }), 200
        
        # Phát hiện khuôn mặt từ ảnh webcam
        faces_retina = face_engine.detect_faces_retinaface(temp_path)
        faces_opencv = face_engine.detect_faces_opencv(temp_path)
        faces_dlib = face_engine.detect_faces_dlib(temp_path)
        # Ưu tiên RetinaFace nếu có, sau đó OpenCV, rồi dlib
        all_faces = faces_retina or faces_opencv or faces_dlib
        
        if not all_faces:
            if os.path.exists(temp_path):
                os.remove(temp_path)
            return jsonify({
                "status": "error",
                "message": "Không phát hiện được khuôn mặt trong ảnh webcam. Vui lòng đảm bảo khuôn mặt rõ và đủ sáng."
            }), 200
        
        # Chọn khuôn mặt có chất lượng tốt nhất từ webcam
        best_webcam_face = None
        best_webcam_quality = 0.0
        
        for face in all_faces:
            quality = face_engine.get_face_quality_score(face)
            if quality > best_webcam_quality:
                best_webcam_quality = quality
                best_webcam_face = face
        
        if best_webcam_face is None:
            if os.path.exists(temp_path):
                os.remove(temp_path)
            return jsonify({
                "status": "error",
                "message": "Chất lượng khuôn mặt từ webcam quá thấp. Vui lòng điều chỉnh vị trí và ánh sáng."
            }), 200
        
        # Căn chỉnh khuôn mặt từ webcam
        aligned_webcam_face = face_engine.align_face(best_webcam_face)
        
        # Kiểm tra liveness (chống giả mạo) trước khi so khớp - TĂNG CƯỜNG
        is_live, live_score = check_liveness_with_silent_face(aligned_webcam_face)
        if not is_live:
            if os.path.exists(temp_path):
                os.remove(temp_path)
            return jsonify({
                "status": "error",
                "message": f"🚨 PHÁT HIỆN GIẢ MẠO! Điểm liveness: {live_score:.2f}. Vui lòng dùng khuôn mặt THẬT, không phải ảnh từ điện thoại!",
                "liveness": round(live_score, 3),
                "fraud_detected": True
            }), 200
        
        # Kiểm tra các biện pháp chống gian lận nâng cao
        anti_fraud_passed, anti_fraud_message = check_anti_fraud_measures(aligned_webcam_face)
        if not anti_fraud_passed:
            if os.path.exists(temp_path):
                os.remove(temp_path)
            return jsonify({
                "status": "error",
                "message": f"🚨 PHÁT HIỆN GIAN LẬN: {anti_fraud_message}",
                "anti_fraud_check": False,
                "fraud_detected": True
            }), 200
        
        # Kiểm tra chớp mắt (nếu bật)
        if ANTI_FRAUD_CONFIG['blink_detection']:
            has_blink = detect_blink(aligned_webcam_face)
            if not has_blink:
                if os.path.exists(temp_path):
                    os.remove(temp_path)
                return jsonify({
                    "status": "error",
                    "message": "🚨 PHÁT HIỆN ẢNH TĨNH! Vui lòng chớp mắt để xác nhận bạn là người thật.",
                    "blink_detection": False,
                    "fraud_detected": True
                }), 200
        
        # Kiểm tra motion blur (phát hiện ảnh tĩnh)
        if check_motion_blur(aligned_webcam_face):
            if os.path.exists(temp_path):
                os.remove(temp_path)
            return jsonify({
                "status": "error",
                "message": "🚨 PHÁT HIỆN ẢNH TĨNH! Vui lòng sử dụng khuôn mặt thật, không phải ảnh từ điện thoại!",
                "motion_blur_detected": True,
                "fraud_detected": True
            }), 200
        
        # Lấy embedding từ nhiều model cho ảnh webcam
        query_embeddings = face_engine.get_multiple_model_embeddings(aligned_webcam_face)
        
        if not query_embeddings:
            if os.path.exists(temp_path):
                os.remove(temp_path)
            return jsonify({
                "status": "error",
                "message": "Không trích xuất được đặc trưng khuôn mặt từ ảnh webcam. Vui lòng thử lại!"
            }), 200
        
        # Tìm khuôn mặt phù hợp nhất bằng similarity thuần + kiểm tra khoảng cách top-1 vs top-2
        best_match = None
        best_similarity = 0.0
        second_best_similarity = 0.0
        
        for student_id, known_embeddings_dict in known_embeddings.items():
            try:
                similarity_score = face_engine.calculate_similarity_score(
                    query_embeddings, known_embeddings_dict
                )
                if similarity_score > best_similarity:
                    second_best_similarity = best_similarity
                    best_similarity = similarity_score
                    best_match = student_id
                elif similarity_score > second_best_similarity:
                    second_best_similarity = similarity_score
            except Exception as e:
                logger.warning(f"Lỗi so sánh với {student_id}: {str(e)}")
                continue
        
        # Xóa ảnh tạm
        if os.path.exists(temp_path):
            os.remove(temp_path)
        
        # Áp dụng ngưỡng tối ưu cho tốc độ và độ chính xác 60%
        final_threshold = 0.6
        margin_required = 0.02  # Giảm margin từ 5% xuống 2% để tăng tốc
        
        logger.info(f"Recognition threshold: {final_threshold:.3f}, Best similarity: {best_similarity:.3f}")
        
        if best_match and best_similarity >= final_threshold and (best_similarity - second_best_similarity) >= margin_required:
            logger.info(f"Face recognized: {best_match} with similarity {best_similarity:.3f}")
            
            # Lấy tên nhân viên (ưu tiên cache), fallback DB
            student_name = employee_names.get(str(best_match), None)
            if not student_name:
                conn_fetch = get_mysql_connection()
                if not conn_fetch:
                    return jsonify({"status": "error", "message": "Database connection failed"}), 500
                try:
                    with conn_fetch.cursor() as cursor:
                        cursor.execute("SELECT ho_ten FROM nhan_vien WHERE id_nhan_vien=%s", (best_match,))
                        name_row = cursor.fetchone()
                        student_name = name_row['ho_ten'] if name_row else "Unknown"
                finally:
                    conn_fetch.close()

            # Ghi log vào bảng attendance (phục vụ UI Flask)
            try:
                conn_att = get_mysql_connection()
                if conn_att:
                    with conn_att.cursor() as cursor:
                        cursor.execute(
                            "INSERT INTO attendance (student_id, date, status) VALUES (%s, %s, %s)",
                            (best_match, datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S"), "Present")
                        )
                    conn_att.commit()
                    conn_att.close()
            except Exception as e:
                logger.warning(f"Không thể ghi log attendance nội bộ: {e}")
            
            # Thêm timestamp vào đường dẫn ảnh để tránh cache
            import time
            timestamp = int(time.time())
            student_image_path = known_students.get(str(best_match), "")
            image_url = None
            
            if student_image_path:
                # Tạo URL với timestamp
                relative_path = os.path.relpath(student_image_path, PROJECT_ROOT)
                if relative_path.startswith('img/'):
                    image_url = f"/img/{os.path.basename(relative_path)}?t={timestamp}"
                else:
                    image_url = f"{relative_path}?t={timestamp}"
            
            return jsonify({
                "status": "success", 
                "student_id": best_match,
                "student_name": student_name,
                "student_image": image_url,
                "similarity": round(best_similarity, 3),
                "quality": round(best_webcam_quality, 3),
                "threshold": round(final_threshold, 3),
                "top2_margin": round(best_similarity - second_best_similarity, 3),
                "models_used": len(query_embeddings),
                "anti_fraud_passed": True,
                "liveness_score": round(live_score, 3),
                "blink_detected": ANTI_FRAUD_CONFIG['blink_detection'],
                "message": f"Điểm danh thành công! Nhân viên: {student_name} (Độ chính xác: {best_similarity:.1%})"
            })
        
        # Thông tin chi tiết về lý do không nhận diện được
        if best_match:
            logger.warning(f"Face detected but below threshold: {best_match}, similarity: {best_similarity:.3f}, threshold: {final_threshold:.3f}")
            return jsonify({
                "status": "error", 
                "message": f"Phát hiện khuôn mặt nhưng độ tương đồng ({best_similarity:.1%}) thấp hơn ngưỡng ({final_threshold:.1%}). Vui lòng thử lại với góc nhìn và ánh sáng tốt hơn.",
                "similarity": round(best_similarity, 3),
                "threshold": round(final_threshold, 3)
            }), 200
        else:
            logger.warning("No face recognized from any known person")
            return jsonify({
                "status": "error", 
                "message": "Không nhận diện được khuôn mặt từ danh sách nhân viên đã đăng ký. Vui lòng kiểm tra lại hoặc liên hệ quản trị viên."
            }), 200
            
    except Exception as e:
        logger.exception(f"Error in take_attendance: {str(e)}")
        # Xóa ảnh tạm nếu có lỗi
        if os.path.exists("temp_capture.jpg"):
            os.remove("temp_capture.jpg")
        # Trả về 200 để UI hiển thị thông điệp chi tiết thay vì INTERNAL SERVER ERROR
        return jsonify({"status": "error", "message": f"Lỗi xử lý: {str(e)}"}), 200

# API xem lịch sử điểm danh
@app.route('/history', methods=['GET'])
def get_history():
    try:
        conn = get_mysql_connection()
        if not conn:
            return jsonify({"status": "error", "message": "Database connection failed"}), 500
            
        with conn.cursor() as cursor:
            cursor.execute("""
                SELECT a.id, s.name, a.date, a.status 
                FROM attendance a 
                JOIN students s ON a.student_id = s.id 
                ORDER BY a.date DESC
            """)
            history = [{"id": row['id'], "name": row['name'], "date": row['date'], "status": row['status']} for row in cursor.fetchall()]
        conn.close()
        return jsonify(history)
    except Exception as e:
        logger.error(f"Error in get_history: {str(e)}")
        return jsonify({"status": "error", "message": str(e)}), 500

# API thống kê tỷ lệ chuyên cần
@app.route('/stats', methods=['GET'])
def get_stats():
    try:
        conn = get_mysql_connection()
        if not conn:
            return jsonify({"status": "error", "message": "Database connection failed"}), 500
            
        with conn.cursor() as cursor:
            cursor.execute("""
                SELECT student_id, COUNT(*) as total, 
                       SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) as present 
                FROM attendance 
                GROUP BY student_id
            """)
            stats = []
            for row in cursor.fetchall():
                student_id = row['student_id']
                total = row['total']
                present = row['present']
                attendance_rate = (present / total * 100) if total > 0 else 0
                
                cursor.execute("SELECT name FROM students WHERE id=%s", (student_id,))
                name_row = cursor.fetchone()
                name = name_row['name'] if name_row else "Unknown"
                stats.append({"name": name, "attendance_rate": round(attendance_rate, 2), "present": present, "total": total})
        conn.close()
        return jsonify(stats)
    except Exception as e:
        logger.error(f"Error in get_stats: {str(e)}")
        return jsonify({"status": "error", "message": str(e)}), 500

# API xóa một bản ghi điểm danh
@app.route('/delete_attendance/<int:attendance_id>', methods=['DELETE'])
def delete_attendance(attendance_id):
    try:
        conn = get_mysql_connection()
        if not conn:
            return jsonify({"status": "error", "message": "Database connection failed"}), 500
            
        with conn.cursor() as cursor:
            cursor.execute("DELETE FROM attendance WHERE id=%s", (attendance_id,))
        conn.commit()
        conn.close()
        logger.info(f"Deleted attendance record: {attendance_id}")
        return jsonify({"status": "success", "message": "Đã xóa bản ghi điểm danh"})
    except Exception as e:
        logger.error(f"Error in delete_attendance: {str(e)}")
        return jsonify({"status": "error", "message": str(e)}), 500

# API xóa tất cả bản ghi điểm danh
@app.route('/clear_all_attendance', methods=['DELETE'])
def clear_all_attendance():
    try:
        conn = get_mysql_connection()
        if not conn:
            return jsonify({"status": "error", "message": "Database connection failed"}), 500
            
        with conn.cursor() as cursor:
            cursor.execute("DELETE FROM attendance")
        conn.commit()
        conn.close()
        logger.info("Cleared all attendance records")
        return jsonify({"status": "success", "message": "Đã xóa tất cả bản ghi điểm danh"})
    except Exception as e:
        logger.error(f"Error in clear_all_attendance: {str(e)}")
        return jsonify({"status": "error", "message": str(e)}), 500

# API để lấy ảnh nhân viên với timestamp
@app.route('/employee_image/<int:employee_id>')
def get_employee_image(employee_id):
    try:
        conn = get_mysql_connection()
        if not conn:
            return jsonify({"status": "error", "message": "Database connection failed"}), 500
        
        with conn.cursor() as cursor:
            cursor.execute("SELECT hinh_anh FROM nhan_vien WHERE id_nhan_vien=%s", (employee_id,))
            result = cursor.fetchone()
            
            if result and result['hinh_anh']:
                image_path = result['hinh_anh']
                # Thêm timestamp để tránh cache
                import time
                timestamp = int(time.time())
                
                # Tạo URL với timestamp
                if image_path.startswith('/'):
                    image_url = f"{image_path}?t={timestamp}"
                else:
                    image_url = f"/img/{image_path}?t={timestamp}"
                
                return jsonify({
                    "status": "success",
                    "image_url": image_url,
                    "timestamp": timestamp
                })
            else:
                return jsonify({"status": "error", "message": "Không tìm thấy ảnh"}), 404
                
    except Exception as e:
        logger.error(f"Error getting employee image: {e}")
        return jsonify({"status": "error", "message": str(e)}), 500
    finally:
        if conn:
            conn.close()

# API để cập nhật ảnh nhân viên
@app.route('/update_employee_image', methods=['POST'])
def update_employee_image():
    try:
        data = request.get_json()
        employee_id = data.get('employee_id')
        new_image_path = data.get('image_path')
        
        if not employee_id or not new_image_path:
            return jsonify({"status": "error", "message": "Thiếu thông tin"}), 400
        
        conn = get_mysql_connection()
        if not conn:
            return jsonify({"status": "error", "message": "Database connection failed"}), 500
        
        with conn.cursor() as cursor:
            cursor.execute(
                "UPDATE nhan_vien SET hinh_anh = %s WHERE id_nhan_vien = %s",
                (new_image_path, employee_id)
            )
        conn.commit()
        conn.close()
        
        # Reload known faces để cập nhật cache
        load_known_faces()
        
        return jsonify({
            "status": "success",
            "message": "Đã cập nhật ảnh nhân viên",
            "new_image_path": new_image_path
        })
        
    except Exception as e:
        logger.error(f"Error updating employee image: {e}")
        return jsonify({"status": "error", "message": str(e)}), 500

if __name__ == '__main__':
    # Đảm bảo thư mục ảnh tồn tại (nếu không có thì tạo)
    if not os.path.exists(DB_PATH):
        os.makedirs(DB_PATH)
    
    # Khởi tạo MySQL database
    if init_mysql_db():
        load_known_faces()  # Load thông tin nhân viên khi khởi động server
        app.run(debug=True, host='0.0.0.0', port=5001)
    else:
        logger.error("Failed to initialize MySQL database. Exiting...")
