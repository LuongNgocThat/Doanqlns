# database.py (Giữ nguyên, nhưng thực tế không dùng trong app.py, có thể xóa nếu không cần)
import sqlite3

def connect_db():
    return sqlite3.connect('attendance.db')

def add_student(student_id, name, class_id):
    conn = connect_db()
    c = conn.cursor()
    c.execute("INSERT OR REPLACE INTO students (id, name, class_id) VALUES (?, ?, ?)", (student_id, name, class_id))
    conn.commit()
    conn.close()

def get_attendance_history():
    conn = connect_db()
    c = conn.cursor()
    c.execute("SELECT s.name, a.date, a.status FROM attendance a JOIN students s ON a.student_id = s.id")
    history = c.fetchall()
    conn.close()
    return history