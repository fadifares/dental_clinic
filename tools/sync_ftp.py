import os
import sys
import json
import ftplib

def get_ftp_connection(config):
    ftp = ftplib.FTP()
    ftp.connect(config['host'], config['port'], timeout=30)
    ftp.login(config['username'], config['password'])
    return ftp

def ensure_remote_dir(ftp, remote_dir):
    parts = [p for p in remote_dir.replace('\\', '/').strip('/').split('/') if p]
    current = '/'
    for part in parts:
        current += part + '/'
        try:
            ftp.cwd(current)
        except ftplib.error_perm:
            try:
                ftp.mkd(current)
                ftp.cwd(current)
            except Exception as e:
                pass

def upload_file(ftp, local_path, remote_path):
    remote_path = remote_path.replace('\\', '/')
    remote_dir = os.path.dirname(remote_path)
    if remote_dir:
        ensure_remote_dir(ftp, remote_dir)
    
    with open(local_path, 'rb') as f:
        filename = os.path.basename(remote_path)
        ftp.cwd(remote_dir if remote_dir else '/')
        ftp.storbinary(f'STOR {filename}', f)
    print(f"Uploaded: {local_path} -> {remote_path}")

def main():
    with open('.vscode/sftp.json', 'r') as f:
        config = json.load(f)
    
    remote_base = config.get('remotePath', '/dental/').rstrip('/')
    
    files_to_upload = sys.argv[1:]
    if not files_to_upload:
        print("No files specified for FTP upload.")
        return
    
    ftp = get_ftp_connection(config)
    try:
        for file_path in files_to_upload:
            norm_path = os.path.normpath(file_path)
            if not os.path.exists(norm_path):
                print(f"File not found locally, skipping: {norm_path}")
                continue
            if os.path.isdir(norm_path):
                for root, _, files in os.walk(norm_path):
                    for file in files:
                        local_full = os.path.join(root, file)
                        rel_path = os.path.relpath(local_full, os.getcwd()).replace('\\', '/')
                        remote_full = f"{remote_base}/{rel_path}"
                        upload_file(ftp, local_full, remote_full)
            else:
                rel_path = os.path.relpath(norm_path, os.getcwd()).replace('\\', '/')
                remote_full = f"{remote_base}/{rel_path}"
                upload_file(ftp, norm_path, remote_full)
        print("All specified files uploaded successfully!")
    finally:
        try:
            ftp.quit()
        except:
            pass

if __name__ == '__main__':
    main()
