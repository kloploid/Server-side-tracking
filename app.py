import os
from flask import Flask, request, jsonify
import sqlite3
from datetime import datetime

app = Flask(__name__)
DATABASE = os.getenv('DATABASE', 'visit_logs.db')

def init_db():
    with sqlite3.connect(DATABASE) as conn:
        cursor = conn.cursor()
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS visit_log (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id TEXT NOT NULL,
                page TEXT NOT NULL,
                visit_time TEXT NOT NULL
            )
        ''')
        conn.commit()

@app.before_first_request
def initialize():
    init_db()

@app.route('/track_visit', methods=['POST'])
def track_visit():
    if request.is_json:
        data = request.get_json()
        user_id = data.get('user_id')
        page = data.get('page')
        visit_time = data.get('visit_time')

        if not user_id or not page or not visit_time:
            return jsonify({'status': 'error', 'message': 'Invalid data'}), 400

        try:
            visit_time = datetime.strptime(visit_time, '%Y-%m-%d %H:%M:%S')
        except ValueError:
            return jsonify({'status': 'error', 'message': 'Invalid date format'}), 400

        with sqlite3.connect(DATABASE) as conn:
            cursor = conn.cursor()
            cursor.execute('''
                INSERT INTO visit_log (user_id, page, visit_time)
                VALUES (?, ?, ?)
            ''', (user_id, page, visit_time.strftime('%Y-%m-%d %H:%M:%S')))
            conn.commit()

        return jsonify({'status': 'success'}), 200
    else:
        return jsonify({'status': 'error', 'message': 'Request must be JSON'}), 400

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
