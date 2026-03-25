# 🎨 Design Templates - Quick Reference Guide

## 📍 Where Everything Is

### Admin Pages (New)
```
/admin/design-templates.php     → Create and manage templates
/admin/design-assets.php         → Manage logos and text styles
/admin/generate-document.php     → Generate personalized documents
```

### Documentation Files (New)
```
/DESIGN_TEMPLATES_SUMMARY.md     → Complete overview (START HERE)
/DESIGN_TEMPLATES_SETUP.md       → Installation & setup
/DESIGN_TEMPLATES_GUIDE.md       → Full feature guide
/DESIGN_TEMPLATES_EXAMPLES.md    → 5 ready-to-use examples
/design-templates-quick-ref.md   → This file
```

### Database Files (New/Updated)
```
/database/nexsoft_hub.sql        → Updated with 5 new tables
/database/design_templates.sql   → Standalone SQL import (optional)
```

### Upload Directories (New)
```
/assets/uploads/logos/           → Logo storage
/assets/uploads/documents/       → Generated documents
```

### Updated Files
```
/admin/layout-header.php         → Added 3 new menu items
```

---

## ⏱️ 5-Minute Quick Start

### 1. Import Database (2 min)
```
→ Open phpMyAdmin
→ Select nexsoft_hub database
→ Go to SQL tab
→ Paste content from /database/design_templates.sql
→ Click Execute
→ ✓ Done!
```

### 2. Create Logo (1 min)
```
→ Admin → Design Assets → Logos & Images
→ Click upload area
→ Select PNG with company logo
→ Give it a name: "Company Logo"
→ Upload
→ ✓ Logo ready!
```

### 3. Create Template (2 min)
```
→ Admin → Design Templates
→ Click "Create New Template"
→ Name: "Completion Certificate"
→ Type: Certificate
→ Select your logo
→ Copy/paste example HTML from DESIGN_TEMPLATES_EXAMPLES.md
→ Paste into Header, Body, Footer sections
→ Save
→ ✓ Template ready!
```

---

## 🎯 Step-by-Step Usage

### Workflow A: Set Up (One-time)
```
1. Import Database Tables
   └─ Run SQL from design_templates.sql
   
2. Upload Company Logo
   └─ Design Assets → Logos & Images
   
3. (Optional) Create Text Styles
   └─ Design Assets → Text Styles
   
4. Create Templates
   └─ Design Templates → Create New
```

### Workflow B: Generate Certificate
```
1. Go to Generate Document
   
2. Select Template
   └─ Click template from grid
   
3. Fill Recipient Info
   └─ Name, Email, Date, Variables
   
4. Review Preview
   └─ Check on right side
   
5. Generate
   └─ Certificate created in database
   
6. View in Issued Documents
   └─ Preview, download, revoke as needed
```

---

## 📚 Documentation Map

```
START HERE
    ↓
DESIGN_TEMPLATES_SUMMARY.md ← Overview of everything
    ├─ Read this first (5-10 min)
    │
    ├─→ DESIGN_TEMPLATES_SETUP.md ← Installation
    │   └─ Checklist style guide
    │
    ├─→ DESIGN_TEMPLATES_GUIDE.md ← Complete guide
    │   └─ Detailed features & tips
    │
    └─→ DESIGN_TEMPLATES_EXAMPLES.md ← Copy-paste templates
        └─ 5 ready templates + tips
```

---

## 🔧 Key Features Checklist

### Design Templates Page
- [x] Create new templates
- [x] Edit existing templates
- [x] Rich text editor (Bold, Italic, Colors, etc.)
- [x] Logo upload and positioning
- [x] Three sections (Header, Body, Footer)
- [x] Variable insertion
- [x] Set default template
- [x] Preview templates
- [x] Delete templates

### Design Assets → Text Styles
- [x] Create custom text styles
- [x] Configure: Font, Size, Weight, Color
- [x] Live preview
- [x] Delete unused styles
- [x] 5 default styles included

### Design Assets → Logos
- [x] Upload logos (PNG, JPG, GIF, WebP)
- [x] View uploaded logos
- [x] Logo preview in table
- [x] Delete unused logos
- [x] File size tracking

### Generate Document
- [x] Template gallery selection
- [x] Recipient information form
- [x] Dynamic variable fields
- [x] Live preview panel
- [x] Auto-generate certificate ID
- [x] Store in database

---

## 🚀 Activation Checklist

- [ ] SQL tables imported (5 tables created)
- [ ] `/assets/uploads/logos/` directory exists
- [ ] `/assets/uploads/documents/` directory exists
- [ ] Admin pages created (3 files)
- [ ] Navigation updated (layout-header.php)
- [ ] Login to admin panel
- [ ] See "Design Templates" menu item
- [ ] See "Design Assets" menu item
- [ ] See "Generate Document" menu item
- [ ] User has "templates" permission
- [ ] Upload test logo
- [ ] Create test template
- [ ] Generate test certificate
- [ ] Certificate appears in "Issued Documents"

---

## 💡 Pro Tips

### Template Creation
- Use images in your HTML with `<img>` tags
- Add line breaks with `<br>`
- Use tables for multi-column layouts
- Leave 40-60px margins top/bottom
- Keep main text 14-16px for readability

### Variables
- Use format: `[Variable Name]`
- Auto-filled: `[Recipient Name]`, `[Date]`, `[Certificate ID]`
- Custom: `[Course Name]`, `[Duration]`, `[Grade]`, etc.
- Case-sensitive: `[name]` ≠ `[Name]`

### Logo Best Practices
- Format: PNG with transparent background
- Size: 150-300px wide
- DPI: 300 DPI
- File size: < 500KB
- Position: Top-center (most professional)

### Styling
- Professional colors: #0066cc (blue), #666666 (gray)
- Fonts: Georgia (serif), Arial (sans-serif)
- Spacing: Use margins for breathing room
- Lines: Subtle 1-2px borders for elegance

---

## 🆘 Troubleshooting Quick Answers

| Problem | Solution |
|---------|----------|
| Menus not showing | Check user permissions (need "templates") |
| Logo not uploading | Check file format & size (PNG, < 5MB) |
| Variables not appearing | Check syntax `[Like This]` in body only |
| Editor crashes | Refresh browser, clear cache |
| Can't find files | Check `/admin/` folder for PHP files |
| Database error | Run SQL from design_templates.sql again |

---

## 🔐 Security Note

All features include:
- ✅ CSRF protection
- ✅ SQL injection prevention
- ✅ XSS protection
- ✅ File upload validation
- ✅ Permission checking
- ✅ User activity logging

---

## 📞 File Reference

| Item | File | Size |
|------|------|------|
| Main Page | admin/design-templates.php | ~400 lines |
| Assets Manager | admin/design-assets.php | ~350 lines |
| Document Gen | admin/generate-document.php | ~300 lines |
| SQL Schema | database/design_templates.sql | ~200 lines |
| Summary Docs | DESIGN_TEMPLATES_*.md | ~4000 lines total |

---

## 🎓 Learning Path

### 5 Minutes
- Read this file
- Check file locations exist
- View admin pages

### 15 Minutes
- Read DESIGN_TEMPLATES_SETUP.md
- Follow checklist
- Import database

### 30 Minutes
- Read DESIGN_TEMPLATES_GUIDE.md
- Create first template
- Generate first certificate

### 1 Hour
- Review DESIGN_TEMPLATES_EXAMPLES.md
- Customize examples
- Set up company templates

### 2 Hours
- Train team members
- Create template library
- Start using in production

---

## 🚀 Next Steps

### Right Now
1. Import database tables
2. Check files are in place
3. Verify admin menu

### Today
1. Upload logo
2. Create first template
3. Generate test certificate

### This Week
1. Train admin team
2. Set up standard templates
3. Start issuing certificates

### Future
1. Add PDF export
2. Send emails
3. Digital signatures
4. Batch generation

---

## 📊 Feature Matrix

| Feature | Status | Page |
|---------|--------|------|
| Template Creation | ✅ Ready | design-templates.php |
| Rich Text Editor | ✅ Ready | design-templates.php |
| Logo Upload | ✅ Ready | design-assets.php |
| Text Styles | ✅ Ready | design-assets.php |
| Document Generation | ✅ Ready | generate-document.php |
| Variable System | ✅ Ready | generate-document.php |
| Live Preview | ✅ Ready | generate-document.php |
| Database Storage | ✅ Ready | 5 new tables |
| Admin Menu | ✅ Ready | layout-header.php |

---

## ✨ What You Can Do Now

✅ Create unlimited certificate templates
✅ Upload company logos
✅ Design custom text styles
✅ Generate personalized certificates
✅ Store all documents in database
✅ Revoke certificates
✅ Track certificate status
✅ Export for printing

---

## 📝 Template Variables Cheat Sheet

```
[Recipient Name]     → Person getting certificate
[Certificate ID]     → Auto-generated unique ID
[Issue Date]         → Date certificate issued
[Course Name]        → Course or program name
[Duration]           → Length of course
[Grade]              → Final grade
[Instructor Name]    → Instructor name
[Department]         → Department
[Project Name]       → Project name
[Any Custom]         → Any [Variable You Add]
```

---

## 🎯 Common Tasks (How-To)

### How to: Upload a Logo
```
Home → Design Assets → Logos & Images
→ Click upload area or drag file
→ Name: "Company Logo"
→ Upload
→ Done!
```

### How to: Create a Certificate
```
Home → Design Templates → Create New Template
→ Fill name, select type
→ Upload/select logo
→ Add content with [Variables]
→ Save
→ Done!
```

### How to: Generate Certificate
```
Home → Generate Document
→ Select template
→ Fill recipient info
→ Check preview
→ Generate
→ Done!
```

---

## 📞 Support Resources

- **Quick Start** → This file
- **Setup Help** → DESIGN_TEMPLATES_SETUP.md
- **Feature Guide** → DESIGN_TEMPLATES_GUIDE.md
- **Examples** → DESIGN_TEMPLATES_EXAMPLES.md
- **Full Summary** → DESIGN_TEMPLATES_SUMMARY.md
- **SQL File** → database/design_templates.sql

---

## ✅ Summary

You now have a complete design templates system ready to:
- 📜 Create professional certificates
- 📄 Generate personalized letters
- 💝 Design appreciation cards
- 🏆 Issue credentials
- 📊 Track issued documents
- 🔐 Verify authenticity

**Everything is ready to use!** 🚀
