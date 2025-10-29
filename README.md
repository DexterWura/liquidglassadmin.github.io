# Liquid Glass Admin Dashboard Template

A beautiful, modern admin dashboard template inspired by Apple's liquid glass (glassmorphism) design aesthetic. Built with pure HTML, CSS, and JavaScript - no frameworks required.

![Liquid Glass Admin Dashboard](https://img.shields.io/badge/Status-Active-success)
![License](https://img.shields.io/badge/License-MIT-blue)

## ✨ Features

- 🎨 **Liquid Glass Design** - Beautiful glassmorphism UI inspired by Apple's design language
- 📱 **Fully Responsive** - Works seamlessly on all screen sizes (mobile, tablet, desktop)
- 📊 **Interactive Charts** - Multiple chart types (line, bar, doughnut) using Chart.js
- 💳 **Dashboard Cards** - Stat cards, info cards, transaction lists, and more
- 🎯 **Modern UI Components** - Clean, modern components ready for customization
- ⚡ **Pure JavaScript** - No framework dependencies, just vanilla JS
- 🌈 **Smooth Animations** - Beautiful transitions and hover effects
- 🎨 **Customizable** - Easy to customize colors, styles, and components

## 🚀 Getting Started

### Prerequisites

- A modern web browser (Chrome, Firefox, Safari, Edge)

### Installation

1. Clone the repository:
```bash
git clone https://github.com/Dexterwura/liquidglassadmin.github.io.git
```

2. Navigate to the project directory:
```bash
cd liquidglassadmin.github.io
```

3. Open `index.html` in your web browser.

## 📁 Project Structure

```
liquid-glass-admin-template/
│
├── index.html              # Main HTML file
├── README.md              # Project documentation
│
└── assets/
    ├── css/
    │   └── style.css      # Main stylesheet
    ├── js/
    │   └── main.js        # Main JavaScript file
    └── images/            # Image assets folder
```

## 🎨 Components

### Dashboard Elements

- **Statistics Cards** - Display key metrics with icons and trend indicators
- **Revenue Chart** - Interactive line chart showing revenue over time
- **Activity Chart** - Bar chart displaying user activity
- **Traffic Sources Chart** - Doughnut chart for traffic breakdown
- **Transaction List** - Recent transactions with user avatars
- **Product Performance** - Product sales with progress bars
- **Sidebar Navigation** - Responsive navigation menu
- **Top Bar** - Search, notifications, and user profile

## 🛠️ Customization

### Colors

Edit the CSS variables in `assets/css/style.css`:

```css
:root {
    --primary-color: #6366f1;
    --secondary-color: #8b5cf6;
    --background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    /* ... more variables */
}
```

### Charts

Modify chart data and styling in `assets/js/main.js`:

```javascript
// Revenue Chart Data
data: [3200, 4500, 3800, 5200, 4900, 6100, 5800]
```

### Navigation Items

Add or modify sidebar navigation items in `index.html`:

```html
<a href="#" class="nav-item">
    <svg class="nav-icon">...</svg>
    <span>Your Item</span>
</a>
```

## 📱 Responsive Breakpoints

- **Desktop**: > 1024px - Full layout with sidebar
- **Tablet**: 768px - 1024px - Adjusted grid layouts
- **Mobile**: < 768px - Collapsible sidebar, stacked layout

## 🌐 Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Opera (latest)

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

## 🤝 Contributing

Contributions, issues, and feature requests are welcome! Feel free to check the [issues page](https://github.com/Dexterwura/liquidglassadmin.github.io/issues).

## 🙏 Acknowledgments

- Inspired by Apple's liquid glass design aesthetic
- Charts powered by [Chart.js](https://www.chartjs.org/)
- Icons from Feather Icons style

## 📧 Contact

Your Name - [@yourusername](https://twitter.com/yourusername)

Project Link: [https://liquidglassadmin.github.io](https://liquidglassadmin.github.io)

---

⭐ If you find this project helpful, please give it a star!
