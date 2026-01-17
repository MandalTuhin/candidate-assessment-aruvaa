# Resume Upload Fix Documentation

## Issue Description

The Laravel assessment application was experiencing a **500 Internal Server Error** when users attempted to upload resume files after completing the assessment. This error prevented successful candidates from completing their application process.

## Root Cause Analysis

### Primary Issue: Nginx Permission Error
The main cause was an **nginx permission denied error** when processing file uploads. The error occurred because:

1. **Nginx temp directory permissions**: Nginx couldn't write to its default temporary client body directory (`/var/lib/nginx/tmp/client_body/`)
2. **Docker container permissions**: The default nginx temp directories weren't properly configured with `www-data` ownership in the Docker container
3. **File upload processing**: When users uploaded resume files, nginx needed to temporarily store the uploaded file before passing it to PHP-FPM, but lacked write permissions

### Secondary Issue: Excessive Questions
An additional issue was discovered where the assessment was showing 40+ questions instead of the intended 10 questions, making testing extremely tedious.

## Error Evidence

The nginx error log showed:
```
2026/01/17 15:43:54 [crit] 31#31: *3 open() "/var/lib/nginx/tmp/client_body/0000000001" failed (13: Permission denied), client: 172.19.0.1, server: _, request: "POST /upload-resume HTTP/1.1"
```

## Solution Implemented

### 1. Fixed Nginx Temp Directory Configuration

**Updated `docker/nginx.conf`** to use custom temp directories with proper permissions:

```nginx
# Configure temp directories with proper permissions
client_body_temp_path /tmp/nginx_client_temp;
proxy_temp_path /tmp/nginx_proxy_temp;
fastcgi_temp_path /tmp/nginx_fastcgi_temp;
uwsgi_temp_path /tmp/nginx_uwsgi_temp;
scgi_temp_path /tmp/nginx_scgi_temp;
```

### 2. Updated Docker Container Setup

**Modified `Dockerfile`** to create temp directories with proper ownership:

```dockerfile
# Create necessary directories and set permissions
RUN mkdir -p /var/www/html/database \
    && touch /var/www/html/database/database.sqlite \
    && mkdir -p /tmp/nginx_client_temp /tmp/nginx_proxy_temp /tmp/nginx_fastcgi_temp /tmp/nginx_uwsgi_temp /tmp/nginx_scgi_temp \
    && chown -R www-data:www-data /var/www/html \
    && chown -R www-data:www-data /tmp/nginx_* \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache \
    && chmod -R 755 /var/www/html/database \
    && chmod -R 755 /tmp/nginx_*
```

### 3. Fixed Question Limit Issue

**Updated `app/Services/AssessmentService.php`** to properly limit questions:

```php
class AssessmentService
{
    private const QUESTIONS_PER_ASSESSMENT = 10;

    public function getQuestionsForAssessment(array $languageIds): Collection
    {
        return $this->questionRepository->getRandomQuestionsByLanguages($languageIds, self::QUESTIONS_PER_ASSESSMENT);
    }
}
```

### 4. Added Comprehensive Logging

Enhanced both `AssessmentController` and `FileUploadService` with detailed logging to help with future debugging:

```php
// Added detailed logging in uploadResume method
Log::info('Resume upload attempt started', [
    'request_data' => $request->all(),
    'files' => $request->allFiles(),
]);
```

## Verification

After implementing the fixes:

1. **Temp directories created successfully**:
   ```bash
   $ docker-compose exec app ls -la /tmp/ | grep nginx
   drwxr-xr-x    1 www-data www-data         0 Jan 17 15:48 nginx_client_temp
   drwxr-xr-x    1 www-data www-data         0 Jan 17 15:48 nginx_fastcgi_temp
   # ... other temp directories
   ```

2. **Resume uploads working**:
   ```
   [2026-01-17 15:48:44] local.INFO: FileUploadService: File stored successfully
   [2026-01-17 15:48:44] local.INFO: Resume upload completed successfully
   ```

3. **Question limit fixed**:
   - Assessment now shows exactly 10 questions instead of 40+
   - Testing the resume functionality is now practical

## Configuration Details

### File Upload Limits
- **PHP**: `upload_max_filesize = 2M`, `post_max_size = 8M`
- **Nginx**: `client_max_body_size 20M`
- **Application**: Validates PDF, DOC, DOCX files up to 2MB

### Storage Configuration
- **Disk**: Uses Laravel's `public` disk
- **Path**: Files stored in `storage/app/public/resumes/`
- **Permissions**: `755` for directories, `644` for files

## Files Modified

1. `docker/nginx.conf` - Added temp directory configuration
2. `Dockerfile` - Added temp directory creation and permissions
3. `app/Services/AssessmentService.php` - Added question limit constant
4. `app/Http/Controllers/AssessmentController.php` - Enhanced logging
5. `app/Services/FileUploadService.php` - Enhanced logging

## Testing

To test the fix:

1. **Start the application**: `docker-compose up --build -d`
2. **Complete assessment**: Take the 10-question assessment
3. **Upload resume**: Upload a PDF/DOC/DOCX file (max 2MB)
4. **Verify success**: Should see "Resume uploaded successfully!" message

## Prevention

To prevent similar issues in the future:

1. **Always configure nginx temp directories** in Docker containers
2. **Set proper file permissions** for web server processes
3. **Test file upload functionality** in containerized environments
4. **Monitor nginx error logs** for permission issues
5. **Use detailed logging** for debugging complex operations

## Related Issues

This fix also resolved:
- Inertia.js middleware autoloading issues (fixed with `composer dump-autoload`)
- Database connection verification
- Storage symlink configuration

---

**Fix Date**: January 17, 2026  
**Status**: ✅ Resolved  
**Impact**: Resume upload functionality fully operational