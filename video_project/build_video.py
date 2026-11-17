import os
import sys
import subprocess
import json
import glob

if hasattr(sys.stdout, 'reconfigure'):
    sys.stdout.reconfigure(encoding='utf-8', errors='replace')
if hasattr(sys.stderr, 'reconfigure'):
    sys.stderr.reconfigure(encoding='utf-8', errors='replace')

# Paths
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
SCENES_DIR = os.path.join(BASE_DIR, "video_project", "scenes")
TEMP_DIR = os.path.join(BASE_DIR, "video_project", "temp")
os.makedirs(TEMP_DIR, exist_ok=True)

# Find the downloads audio folder
DOWNLOADS = r"C:\Users\fady2\Downloads"
audio_dir = None
for item in os.listdir(DOWNLOADS):
    p = os.path.join(DOWNLOADS, item)
    if os.path.isdir(p):
        try:
            if any("1111" in f for f in os.listdir(p)):
                audio_dir = p
                break
        except Exception:
            pass

if not audio_dir:
    raise RuntimeError("Could not find audio directory in Downloads")

print(f"Audio directory: {audio_dir}")

# Map scenes to images and audio files
scenes_config = [
    {
        "id": 1,
        "image": os.path.join(SCENES_DIR, "scene_1_dashboard.png"),
        "audio_pattern": "*1111*.mp3",
        "zoom": "in",
    },
    {
        "id": 2,
        "image": os.path.join(SCENES_DIR, "scene_2_patient_profile.png"),
        "audio_pattern": "*222*.mp3",
        "zoom": "out",
    },
    {
        "id": 3,
        "image": os.path.join(SCENES_DIR, "scene_3_odontogram.png"),
        "audio_pattern": "*333*.mp3",
        "zoom": "in",
    },
    {
        "id": 4,
        "image": os.path.join(SCENES_DIR, "scene_4_appointments_main.png"),
        "audio_pattern": "*444*.mp3",
        "zoom": "out",
    },
    {
        "id": 5,
        "image": os.path.join(SCENES_DIR, "scene_5_billing.png"),
        "audio_pattern": "*555*.mp3",
        "zoom": "in",
    },
    {
        "id": 6,
        "image": os.path.join(SCENES_DIR, "scene_6_security.png"),
        "audio_pattern": "*666*.mp3",
        "zoom": "out",
    },
    {
        "id": 7,
        "image": os.path.join(SCENES_DIR, "scene_7_reports.png"),
        "audio_pattern": "*777*.mp3",
        "zoom": "in",
    },
]

def get_audio_duration(path):
    cmd = [
        "ffprobe", "-v", "error",
        "-show_entries", "format=duration",
        "-of", "json", path
    ]
    res = subprocess.check_output(cmd)
    return float(json.loads(res)["format"]["duration"])

segment_files = []

for scene in scenes_config:
    sid = scene["id"]
    # Find audio file
    matches = glob.glob(os.path.join(audio_dir, scene["audio_pattern"]))
    if not matches:
        raise FileNotFoundError(f"Audio for scene {sid} pattern {scene['audio_pattern']} not found!")
    audio_path = matches[0]
    
    duration = get_audio_duration(audio_path)
    # Add a slight 0.5s pause at the end of each scene for natural breathing between segments
    total_dur = duration + 0.5
    
    fps = 30
    total_frames = int(total_dur * fps)
    
    img_path = scene["image"]
    seg_output = os.path.join(TEMP_DIR, f"scene_{sid}.mp4")
    segment_files.append(seg_output)
    
    print(f"--- Processing Scene {sid} (Duration: {duration:.2f}s, Total: {total_dur:.2f}s) ---")
    
    # Smooth Ken Burns zoompan filter:
    # zoom in: 1.0 -> 1.06
    # zoom out: 1.06 -> 1.0
    if scene["zoom"] == "in":
        zoom_expr = f"min(pzoom+0.0003,1.06)"
        x_expr = "iw/2-(iw/zoom/2)"
        y_expr = "ih/2-(ih/zoom/2)"
    else:
        zoom_expr = f"if(eq(on,1),1.06,max(pzoom-0.0003,1.0))"
        x_expr = "iw/2-(iw/zoom/2)"
        y_expr = "ih/2-(ih/zoom/2)"
        
    vf = f"scale=1920:1080:force_original_aspect_ratio=decrease,pad=1920:1080:(ow-iw)/2:(oh-ih)/2,zoompan=z='{zoom_expr}':x='{x_expr}':y='{y_expr}':d={total_frames}:s=1920x1080:fps={fps}"

    # Generate video clip with audio
    cmd = [
        "ffmpeg", "-y",
        "-loop", "1", "-i", img_path,
        "-i", audio_path,
        "-filter_complex",
        f"[0:v]{vf},format=yuv420p[v];[1:a]apad=pad_dur=0.5[a]",
        "-map", "[v]",
        "-map", "[a]",
        "-t", f"{total_dur:.3f}",
        "-c:v", "libx264",
        "-preset", "faster",
        "-crf", "18",
        "-c:a", "aac",
        "-b:a", "192k",
        "-movflags", "+faststart",
        seg_output
    ]
    
    subprocess.check_call(cmd)
    print(f"Scene {sid} generated: {seg_output}")

# Create concat list
concat_list_path = os.path.join(TEMP_DIR, "concat.txt")
with open(concat_list_path, "w", encoding="utf-8") as f:
    for seg in segment_files:
        f.write(f"file '{seg.replace(chr(92), '/')}'\n")

final_output = os.path.join(audio_dir, "dental_pro_showcase.mp4")
print(f"\nConcatenating all scenes into: {final_output}")

concat_cmd = [
    "ffmpeg", "-y",
    "-f", "concat",
    "-safe", "0",
    "-i", concat_list_path,
    "-c", "copy",
    "-movflags", "+faststart",
    final_output
]

subprocess.check_call(concat_cmd)
print("\n=== VIDEO ASSEMBLY COMPLETED SUCCESSFULLY! ===")
print(f"Output saved to: {final_output}")
