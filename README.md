# BeBo
BeBo is a simple-to-use free temporary file hosting service. It lets you share your photos, documents, music, videos and more with others online with no ads, account sign-up or tracking.

![Screenshot of BeBo](https://github.com/arm64x/BeBo/blob/main/ScreenShot.png)

# Key Features
1. Drag and Drop Support
BeBo simplifies file uploads with an intuitive drag-and-drop interface, making it user-friendly for everyone.

2. Multi-file Upload
Upload multiple files simultaneously to save time and effort.

3. Upload Size Limit
Set a maximum file size for uploads to ensure controlled and efficient usage.

4. Auto-expiry for Files
Configure files to automatically delete after a specified period, ensuring temporary storage and better security.

# Installation Guide
Follow these simple steps to get BeBo up and running:

1. Edit the Configuration File
Open the config.php file and configure the following options:
- domain: Set your domain name.
- max_file_size: Define the maximum allowed file size for uploads.
- allowed_extensions: Specify the file types permitted for upload (e.g., .jpg, .png, .pdf).
- upload_dir: Set the directory where uploaded files will be stored.
- default_expiry: Define the default expiry time for uploaded files.
- db_file: Specify the database file for tracking uploads and expirations.
  
2. Set Up Cron Job
Schedule a cron job to run the cleanup.php script every second. This ensures files that have reached their expiry time are automatically deleted.
Example cron job:
* * * * * php /path/to/cleanup.php
       

