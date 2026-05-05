from flask import Flask,request,jsonify
import numpy as np

app=Flask(__name__)

@app.route('/ai',methods=['POST'])
def ai():
    data=request.json['data']
    skor=sum(data)/len(data)*100
    return jsonify({"hasil":skor})

app.run()