import mysql.connector

conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",  # If you set a password, enter it here.
    database="face_recognition"
)

if conn.is_connected():
    print("✅ Connected to MySQL successfully!")
else:
    print("❌ Connection failed!")
