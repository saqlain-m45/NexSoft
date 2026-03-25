# Design Templates - Letters & Certificates Feature Guide

## Overview
A complete system for creating, managing, and generating professional letters and certificates with customizable designs, logos, text styles, and dynamic content.

## Features Included

### 1. **Design Templates** (`design-templates.php`)
Create and manage reusable certificate and letter templates with:
- **Rich Text Editor** with formatting toolbar
  - Bold, Italic, Underline formatting
  - Heading levels (H1, H2, Paragraph)
  - Text alignment (Left, Center, Right, Justify)
  - Lists (Unordered)
  - Text and background colors
  - Variable insertion (placeholders)

- **Template Sections**
  - Header: Company name, title, decorations
  - Body: Main certificate text, dynamic variables
  - Footer: Signatures, dates, footer text

- **Logo Management**
  - Upload custom company logo
  - Position control (Top-left, Top-center, Top-right, Center)
  - Size adjustment (in pixels)

- **Template Types**
  - Certificate
  - Letter
  - Appreciation Card
  - Credentials

- **Features**
  - Set default template per type
  - Activate/Deactivate templates
  - Full CRUD operations
  - Template preview

### 2. **Design Assets** (`design-assets.php`)
Centralized management of reusable design components:

#### Text Styles
- Create custom text style presets
- Configurable properties:
  - Font Family (Arial, Georgia, Times New Roman, Courier New, Verdana, Comic Sans)
  - Font Size (8-72 pixels)
  - Font Weight (Normal, Bold, Medium, Semi-Bold, Lighter)
  - Text Color
  - Line Height
  - Text Alignment
  - Background Color
- Live preview of styles
- Default styles included

#### Logo Management
- Upload company logos and images
- Supported formats: PNG, JPG, GIF, WebP
- Max file size: 5MB
- File size and dimensions tracking
- Image preview in table
- Delete unused logos
- Recommended specs: PNG with transparent background, 300 DPI, 150-300px wide

### 3. **Document Generation** (`generate-document.php`)
Create personalized certificates and letters from templates:

#### Workflow
1. **Select Template** - Choose from active templates
2. **Fill Information**
   - Recipient Name (required)
   - Recipient Email
   - Issue Date
   - Custom Variables (any [Variable] from template)
3. **Live Preview** - See document as you fill information
4. **Generate** - Creates document record

#### Dynamic Variables
Supported template variables:
- `[Recipient Name]` - Automatically filled from form
- `[Date]` - Issue date
- `[Certificate ID]` - Auto-generated unique ID
- `[Course Name]` - Custom variable
- `[Duration]` - Custom variable
- `[Grade]` - Custom variable
- `[Instructor Name]` - Custom variable
- `[Issue Date]` - Custom variable
- **Any custom variable** - Add `[Variable Name]` in template, it appears in form

### 4. **Database Structure**

#### `design_templates`
Stores certificate and letter template definitions
- Template name, type, description
- Logo image path and positioning
- HTML content for header, body, footer
- Active/Default status
- Created by user tracking

#### `template_logos`
Manages logo assets
- File path and size info
- Uploaded by user
- Is active status

#### `text_styles`
Stores reusable text style presets
- Font properties
- Color settings
- CSS class names
- Default styles

#### `design_elements`
Additional design components (extensible)
- Element type (text, line, box, image, shape)
- Position and size
- Style references
- Editability flag

#### `issued_documents`
Stores generated certificates and letters
- Unique document ID
- Recipient information
- Generated content
- Status tracking (active, revoked, expired)
- PDF export path
- Verification token

## How to Use

### Step 1: Upload Logos
1. Go to **Admin → Design Assets → Logos & Images**
2. Click in the dashed upload area or drag and drop
3. Give the logo a name (e.g., "Company Logo 2024")
4. Upload PNG files with transparent backgrounds for best results

### Step 2: Create Text Styles (Optional)
1. Go to **Admin → Design Assets → Text Styles**
2. Click "New Text Style"
3. Set properties:
   - Name: "Title Style", "Body Style", etc.
   - Type: Heading, Body, Footer, or Accent
   - Font family and size
   - Colors and alignment
4. Preview updates in real-time
5. Save

### Step 3: Create Design Template
1. Go to **Admin → Design Templates**
2. Click "Create New Template"
3. **Basic Information**
   - Template Name: "Completion Certificate 2024"
   - Type: Certificate
   - Description: Optional details

4. **Logo Setup**
   - Upload or select existing logo
   - Choose position (Top-center recommended)
   - Set width (150-200px typical)

5. **Content Editing**
   - Use the rich text editor toolbar for formatting
   - **Header**: Add certificate title
     ```
     This is to Certify That
     Certificate of Achievement
     ```
   - **Body**: Add main text with variables
     ```
     <strong>[Recipient Name]</strong> has successfully completed
     the <em>[Course Name]</em> course
     Completed on: [Issue Date]
     Certificate ID: [Certificate ID]
     ```
   - **Footer**: Add signatures and dates
     ```
     Director: _______________  Date: _______________
     ```

6. **Publishing**
   - Check "Active Template" to enable it
   - Check "Set as Default" to make it default for this type
   - Save

### Step 4: Generate Documents
1. Go to **Admin → Generate Document**
2. Select template from grid
3. Fill recipient information:
   - Name: John Doe
   - Email: john@example.com
   - Date: (auto-filled to today)
4. Fill any custom variables from your template
5. View live preview on right
6. Click "Generate Document"

### Step 5: Manage Issued Documents
1. Go to **Admin → Issued Documents**
2. View all generated certificates/letters
3. Download, preview, or revoke as needed

## Template Variable Examples

### Certificate Template
```html
<!-- Header -->
<h1>Certificate of Achievement</h1>
<p>This certifies that</p>

<!-- Body -->
<p>
  <strong style="font-size: 20px;">[Recipient Name]</strong>
</p>
<p>has successfully completed the course</p>
<p>
  <strong>[Course Name]</strong>
</p>
<p>Duration: [Duration] | Grade: [Grade]</p>
<p>Certificate ID: <code>[Certificate ID]</code></p>

<!-- Footer -->
<table style="width: 100%; margin-top: 40px;">
  <tr>
    <td>Instructor: [Instructor Name]</td>
    <td>Date: [Issue Date]</td>
  </tr>
</table>
```

## Tips & Best Practices

1. **Logo Upload**
   - Use PNG format with transparent background
   - Keep dimensions around 150-300px wide
   - Compress images to keep file size under 500KB

2. **Text Editor**
   - Use headings for section titles
   - Keep body text readable (14-16px)
   - Use colors sparingly for emphasis only
   - Test preview before saving

3. **Variables**
   - Use clear variable names: `[Course Name]`, not `[coarse]`
   - Document custom variables in template description
   - Variables are case-sensitive in templates

4. **Template Management**
   - Create one default template per type
   - Name templates descriptively: "Completion Certificate 2024"
   - Add descriptions for team reference

5. **Document Generation**
   - Double-check recipient names and emails
   - Set correct issue dates
   - Use consistent naming conventions

## Table Structure Reference

```
design_templates (Master templates)
├── id
├── name
├── type (certificate, letter, appreciation, credentials)
├── description
├── logo_image (path)
├── logo_position (top-left, top-center, etc.)
├── header_html
├── body_html (contains [Variables])
├── footer_html
├── is_active
├── is_default
└── created_by

template_logos (Logo assets)
├── id
├── name
├── file_path
├── file_size
├── width, height
└── is_active

text_styles (Reusable styles)
├── id
├── name
├── style_type (heading, body, footer, accent)
├── font_family
├── font_size
├── font_weight
├── font_color
├── text_align
└── css_class

issued_documents (Generated documents)
├── id
├── document_id (unique)
├── recipient_name
├── recipient_email
├── type
├── body_content
├── template_id
├── issue_date
├── status (active, revoked)
└── created_at
```

## Troubleshooting

### Logo not showing in preview
- Check file path is correct
- Ensure image format is supported (PNG, JPG, GIF, WebP)
- Verify file was uploaded successfully to `/assets/uploads/logos/`

### Variables not appearing in form
- Make sure variable syntax is exactly: `[Variable Name]`
- Check variable is in body_html (header/footer variables ignored)
- Refresh page if needed
- Case-sensitive: `[Course name]` ≠ `[Course Name]`

### Text formatting lost
- The editor uses HTML - formatting is preserved
- Complex CSS may not work in all contexts
- Stick to basic formatting (bold, italic, colors, alignment)

### File upload fails
- Check file size (max 5MB)
- Verify file format (PNG, JPG, GIF, WebP)
- Ensure `/assets/uploads/logos/` directory exists and is writable
- Check file permissions

## File Locations
- Templates Admin: `/admin/design-templates.php`
- Assets Admin: `/admin/design-assets.php`
- Document Generation: `/admin/generate-document.php`
- Logo Uploads: `/assets/uploads/logos/`
- Document Exports: `/assets/uploads/documents/`

## Security Notes
- All file uploads are validated for type and size
- SQL injection prevention via prepared statements
- CSRF protection on all forms
- User tracking on all operations

## Future Enhancements
- PDF export functionality
- Email distribution
- Batch generation
- Digital signatures
- QR code generation
- Template versioning
- Multi-language support
