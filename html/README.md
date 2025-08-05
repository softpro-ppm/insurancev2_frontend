# Insurance Management System 2.0

A modern, responsive frontend application for managing insurance policies with Material 3 + Glassmorphism design.

## 🎨 Design Features

- **Material 3 + Glassmorphism Theme**: Clean, modern design with frosted glass effects
- **Dark/Light Mode Toggle**: Seamless theme switching with persistent state
- **Responsive Design**: Works perfectly on desktop, tablet, and mobile devices
- **Smooth Animations**: Elegant transitions and hover effects throughout

## 🎯 Color Scheme

- **Background**: #F9FAFB (Light) / #0F172A (Dark)
- **Cards**: Frosted glass effect with rgba(255, 255, 255, 0.7)
- **Text**: #111827 (Light) / #F1F5F9 (Dark)
- **Primary Accent**: #4F46E5 (Blue Indigo)
- **Secondary Accent**: #10B981 (Emerald Green)

## 🚀 Features

### Dashboard
- **4 Dashboard Cards**: Premium, Policies, Renewals, Revenue with current month and FY data
- **Interactive Charts**: 
  - Bar chart showing Premium vs Revenue vs Policies
  - Pie chart displaying insurance distribution
- **Advanced Data Table**: 
  - 50 dummy policies with search, pagination, and sorting
  - Configurable rows per page (10, 30, 50, 100)
  - Sortable columns with visual indicators

### Navigation
- **Fixed Top Bar**: Logo, theme toggle, add policy button, profile dropdown
- **Collapsible Sidebar**: Dashboard, Policies, Renewals, Follow Ups, Reports, Agents, Settings
- **Profile Management**: Admin/Agent/Reception roles with logout option

### Policy Management
- **Add/Edit Policy Modal**: Comprehensive form with 4 sections:
  - Customer Information (Name, Phone, Email)
  - Vehicle Information (Number, Owner, Type)
  - Insurance Information (Type, Company, Dates, Premium, Revenue)
  - Document Upload (Policy Copy, RC, Aadhar, PAN)
- **Auto-calculations**: End date automatically set to 1 year from start date
- **Form Validation**: Required field validation with visual feedback

### Agent Management
- **Add Agent Modal**: Name, Phone, Email, auto-generated User ID, Password
- **Agent Cards**: Visual display of agent information with avatars
- **Auto User ID**: Generated from phone number for easy identification

### Data Management
- **Search Functionality**: Real-time search across policy data
- **Sorting**: Click column headers to sort data
- **Pagination**: Navigate through large datasets efficiently
- **Dummy Data**: 50 realistic policies and 5 agents for demonstration

## 🛠️ Technical Stack

- **HTML5**: Semantic markup with accessibility features
- **CSS3**: Modern styling with CSS Grid, Flexbox, and custom properties
- **jQuery 3.7.1**: DOM manipulation and event handling
- **Chart.js**: Interactive charts and data visualization
- **Font Awesome 6.4.0**: Icon library for consistent UI elements
- **Google Fonts (Inter)**: Modern, readable typography

## 📁 File Structure

```
insurancev2_frontend/
├── index.html          # Main HTML structure
├── styles.css          # Complete CSS styling with themes
├── script.js           # JavaScript functionality
└── README.md           # Documentation
```

## 🎮 Usage

### Getting Started
1. Open `index.html` in a modern web browser
2. The application will load with dummy data automatically
3. Explore different sections using the sidebar navigation

### Key Interactions
- **Theme Toggle**: Click the moon/sun icon in the top bar
- **Add Policy**: Click "Add New Policy" button or use the modal
- **Search**: Use the search box in the data table
- **Sort**: Click column headers to sort data
- **Pagination**: Use page numbers or navigation arrows
- **Sidebar**: Click the hamburger menu to collapse/expand

### Form Features
- **Auto-completion**: User ID auto-generated from phone number
- **Date Calculation**: End date automatically calculated from start date
- **File Upload**: Support for PDF, JPG, JPEG, PNG files
- **Validation**: Required fields marked with asterisks

## 🎨 Design Principles

### Material 3
- **Elevation**: Cards with subtle shadows and depth
- **Color System**: Consistent color palette with semantic meaning
- **Typography**: Clear hierarchy with Inter font family
- **Spacing**: Consistent 8px grid system

### Glassmorphism
- **Backdrop Blur**: Frosted glass effect on cards and modals
- **Transparency**: Semi-transparent backgrounds
- **Borders**: Subtle borders with low opacity
- **Layering**: Multiple transparent layers for depth

## 📱 Responsive Design

- **Desktop**: Full sidebar, multi-column layouts
- **Tablet**: Collapsible sidebar, adjusted grid layouts
- **Mobile**: Stacked layouts, full-width components

## 🔧 Customization

### Adding New Insurance Types
1. Update the policy form in `index.html`
2. Add new options to the insurance type dropdown
3. Update the dummy data generation in `script.js`

### Modifying Color Scheme
1. Edit CSS custom properties in `styles.css`
2. Update chart colors in `script.js`
3. Ensure dark theme compatibility

### Adding New Features
1. Follow the existing code structure
2. Use consistent naming conventions
3. Maintain theme compatibility
4. Add proper error handling

## 🚀 Future Enhancements

- **Backend Integration**: API endpoints for data persistence
- **User Authentication**: Login/logout with role-based access
- **Real-time Updates**: WebSocket integration for live data
- **Export Features**: PDF/Excel export for reports
- **Advanced Analytics**: More detailed charts and insights
- **Mobile App**: Progressive Web App (PWA) capabilities

## 📄 License

This project is created for demonstration purposes. Feel free to use and modify as needed.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📞 Support

For questions or support, please refer to the code comments or create an issue in the repository.

---

**Built with ❤️ using modern web technologies** 