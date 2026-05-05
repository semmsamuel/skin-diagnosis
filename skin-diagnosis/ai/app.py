from fastapi import FastAPI, UploadFile, File, HTTPException
from fastapi.middleware.cors import CORSMiddleware
import uvicorn
from PIL import Image
import io
import random

app = FastAPI(title="Skin Diagnosis AI API")

# Enable CORS for frontend integration
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# List of example skin conditions for mock prediction
SKIN_CONDITIONS = [
    {"label": "Eczema", "confidence": 0.85, "description": "Kondisi kulit yang menyebabkan kulit menjadi merah, gatal, dan meradang."},
    {"label": "Melanoma", "confidence": 0.92, "description": "Jenis kanker kulit yang berkembang dari melanosit."},
    {"label": "Psoriasis", "confidence": 0.78, "description": "Penyakit autoimun kronis yang menyebabkan penumpukan sel kulit secara cepat."},
    {"label": "Acne", "confidence": 0.95, "description": "Kondisi kulit yang terjadi ketika folikel rambut tersumbat oleh minyak dan sel kulit mati."},
    {"label": "Normal", "confidence": 0.99, "description": "Kulit terlihat sehat dan normal."}
]

@app.get("/")
def read_root():
    return {"message": "Skin Diagnosis AI API is running!"}

@app.post("/predict")
async def predict(file: UploadFile = File(...)):
    # Validate file type
    if not file.content_type.startswith("image/"):
        raise HTTPException(status_code=400, detail="File uploaded is not an image.")
    
    try:
        # Read image
        contents = await file.read()
        image = Image.open(io.BytesIO(contents))
        
        # Here you would normally:
        # 1. Preprocess the image (resize, normalize, etc.)
        # 2. Load your model (e.g., model.predict())
        # 3. Get the prediction
        
        # Mocking the AI prediction logic for now
        prediction = random.choice(SKIN_CONDITIONS)
        prediction["confidence"] = round(random.uniform(0.7, 0.99), 2)
        
        return {
            "status": "success",
            "filename": file.filename,
            "prediction": prediction
        }
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Error processing image: {str(e)}")

if __name__ == "__main__":
    uvicorn.run(app, host="0.0.0.0", port=8000)
