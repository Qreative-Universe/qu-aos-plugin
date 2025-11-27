# QU Simple AOS
Lightweight AOS (Animate On Scroll) loader plugin for WordPress.  
Optimized for GeneratePress and GenerateBlocks users who want clean, fast, and modern scroll animations.

---

## 🚀 Overview

**QU Simple AOS** is a minimal WordPress plugin that loads the AOS (Animate On Scroll) library and provides a simple admin settings page to manage initialization options.

- Load AOS via **CDN** or **Local files**
- Customize **duration**, **easing**, **offset**, **delay**, **once**, **mirror**
- Option to **disable AOS on mobile devices**
- Fully compatible with **GenerateBlocks** and the WordPress Block Editor
- Zero bloat – only the essential code
- Ideal for performance-focused themes such as **GeneratePress**, **Astra**, etc.

---

## ✨ Features

| Feature | Description |
|--------|-------------|
| **AOS library auto-loader** | Load from CDN or local plugin assets |
| **AOS init manager** | Configure all global AOS parameters |
| **Mobile disable option** | Disable AOS entirely on mobile (`wp_is_mobile()`) |
| **Data attribute support** | `data-aos`, `data-aos-delay`, `data-aos-duration`, etc. |
| **GenerateBlocks friendly** | Add attributes through block HTML attributes |
| **Ultra lightweight** | No unnecessary scripts or admin bloat |

---

## 🛠 Installation

1. Download or clone this repository.
2. Upload the plugin folder to the following directory:

```
/wp-content/plugins/qu-simple-aos/
```

3. Go to **WordPress Admin → Plugins** and activate **QU Simple AOS**.
4. Open **Settings → QU Simple AOS** to configure the plugin.

---

## ⚙️ Usage

### 1. Configure Settings

Go to:

```
Settings → QU Simple AOS
```

Available options:

- CDN / Local script loading  
- Duration (ms)  
- Easing function  
- Offset (px)  
- Delay (ms)  
- Once  
- Mirror  
- Disable AOS on mobile devices  

---

### 2. Add AOS attributes to HTML

Example:

```html
div data-aos="fade-up" data-aos-delay="200"
    Your content appears here.
/div
```

(Angle brackets removed inside canvas to avoid auto-rendering)

---

### 3. Using with GenerateBlocks

Select any block → **Advanced → HTML Attributes**

Example:

```
data-aos="fade-up" data-aos-delay="150"
```

---

## 📂 Directory Structure

```
qu-simple-aos/
│
├─ assets/
│   ├─ css/
│   │   └─ aos.css
│   └─ js/
│       └─ aos.js
│
├─ qu-simple-aos.php
└─ README.md
```

---

## 🌐 AOS Official Documentation

Detailed animations, settings, and advanced usage:

https://michalsnik.github.io/aos/

---

## 🧩 Supported AOS Attributes

You can apply these attributes directly to any HTML element or inside GenerateBlocks using **HTML Attributes**.

| Attribute | Example | Description |
|----------|----------|-------------|
| `data-aos` | `fade-up` | Animation type |
| `data-aos-delay` | `200` | Delay before animation starts |
| `data-aos-duration` | `1500` | Per-element duration |
| `data-aos-offset` | `300` | Offset trigger value |
| `data-aos-easing` | `ease-in-sine` | Custom easing |
| `data-aos-anchor` | `.hero-section` | Set animation anchor |
| `data-aos-anchor-placement` | `top-bottom` | Anchor trigger position |
| `data-aos-once` | `true` | Run only once |

Example:

```html
div data-aos="fade-up" data-aos-delay="200" data-aos-duration="1200"
    Content appears here.
/div
```

(Angle brackets removed for canvas)

---

## 📱 Mobile Handling

QU Simple AOS includes an optional setting to disable animations on mobile.

When enabled:

- AOS CSS/JS is **not loaded on mobile**
- Faster load performance
- Better scrolling smoothness
- Avoids over-animation on small screens
- Reduces layout shifts and improves Core Web Vitals

This uses WordPress's built-in mobile detection:

```
wp_is_mobile()
```

---

## 🧑‍💻 Author

**QU – WordPress & SEO Specialist**  
Focused on high-performance WP builds, GeneratePress frameworks, and SEO-optimized systems.

---

## 📄 License (MIT)

MIT License

Copyright (c) 2025 QU

Permission is hereby granted, free of charge, to any person obtaining a copy  
of this software and associated documentation files (the “Software”), to deal  
in the Software without restriction, including without limitation the rights  
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell  
copies of the Software, and to permit persons to whom the Software is  
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included  
in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR  
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,  
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE  
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER  
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,  
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN  
THE SOFTWARE.

---

## 🤝 Contributing

Issues and pull requests are welcome.  
If you have suggestions for improvements or new features, feel free to contribute.

---

## ⭐ Support the Project

If you find this plugin useful, please consider giving the repository a **Star**.  
Your support helps the project grow and encourages further development.
