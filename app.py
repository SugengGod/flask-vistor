from flask import Flask, jsonify, request
from flask_cors import CORS
import pickle
import numpy as np
import mysql.connector
from mysql.connector import Error
from dotenv import load_dotenv
import os

app = Flask(__name__)
CORS(app)

load_dotenv()

def save_to_database(data, result):
    try:
        
        nama_host = os.getenv('DB_HOST')
        nama_user = os.getenv('DB_USER')
        nama_password = os.getenv('DB_PASSWORD')
        nama_database = os.getenv('DB_NAME')
        
        # Mengatur koneksi ke database MySQL
        connection = mysql.connector.connect(
            host=nama_host,       # Ganti dengan host Anda
            user=nama_user,            # Ganti dengan username Anda
            password=nama_password,            # Ganti dengan password Anda
            database=nama_database  # Ganti dengan nama database Anda
        )

        if connection.is_connected():
            print("Berhasil terhubung ke database MySQL")

            # Menyimpan data ke tabel
            cursor = connection.cursor()
            query = """
                INSERT INTO data_validasi_2 (lpg, ch4, h2, hasil_validasi)
                VALUES (%s, %s, %s, %s)
            """
            cursor.execute(query, (data[0], data[1], data[2], result))

            # Commit perubahan
            connection.commit()
            print("Data berhasil disimpan ke database")

    except Error as e:
        print("Error saat menyimpan ke database", e)

    finally:
        if connection.is_connected():
            cursor.close()
            connection.close()
            print("Koneksi ke MySQL ditutup")

def validateSVM(fields):
    filename = 'data/svm_model(mq2).pkl'  # Lokasi model SVM Anda

    X_test = [fields]

    # Membuka dan memuat model SVM dari file
    with open(filename, 'rb') as file:
        loaded_model = pickle.load(file)

    # Mengonversi input menjadi array NumPy
    datates = np.array(X_test)
    
    # Melakukan prediksi menggunakan model SVM
    y_pred = loaded_model.predict(datates)

    # Mengembalikan hasil prediksi
    if y_pred == 0:
        return 'AMAN'
    elif y_pred == 1:
        return 'TIDAK AMAN'

@app.route('/validate', methods=['POST'])
def validate_data():
    try:
        # Mengambil data dari request JSON
        data = request.json

        # Pastikan data diterima dalam format list atau array
        if not isinstance(data, list):
            return jsonify({"error": "Data harus berupa list"}), 400

        # Validasi data menggunakan SVM
        result = validateSVM(data)

        # Simpan data dan hasil ke database
        save_to_database(data, result)

        # Membentuk output dalam format JSON
        output = {
            "data": data,
            "hasil_validasi": result
        }

        return jsonify(output)
    except Exception as e:
        return jsonify({"error": str(e)}), 500

app.run(debug=True)
#if __name__ == "__main__":
#    app.run(host='0.0.0.0', port=5000, debug=True)
