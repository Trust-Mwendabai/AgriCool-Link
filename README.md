# AgriCool Link

AgriCool Link is a web-based platform designed to connect Zambian farmers with cold storage facilities and direct market access. The platform aims to solve key challenges faced by farmers in Zambia, including post-harvest losses, price instability, and limited market access.

## Features

### For Farmers
- **Product Management**: Upload and manage agricultural products
- **Storage Booking**: Book cold storage facilities for produce
- **Market Access**: Direct access to buyers, including supermarkets and processors
- **Price Monitoring**: Track market prices and trends
- **Order Management**: Track orders and manage deliveries
- **Analytics Dashboard**: Monitor sales, storage usage, and earnings

### For Storage Providers
- **Storage Unit Management**: Monitor and manage cold storage units
- **Temperature Monitoring**: Real-time temperature tracking
- **Power Management**: Track power sources (main power, generator, solar)
- **Booking Management**: Handle storage space bookings
- **Analytics Dashboard**: Monitor unit utilization and revenue
- **Maintenance Scheduling**: Track and schedule unit maintenance

### For Buyers
- **Product Discovery**: Browse and search for agricultural products
- **Order Management**: Place and track orders
- **Storage Integration**: Coordinate with storage facilities
- **Analytics Dashboard**: Monitor purchases and spending
- **Supplier Ratings**: Rate and review farmers

### Marketplace Features
- **Product Categories**:
  - Vegetables (tomatoes, cabbage, peppers, carrots)
  - Fruits (mangoes, bananas, oranges, pineapples)
  - Grains (maize, rice, sorghum, millet)
  - Tubers (sweet potatoes, cassava, irish potatoes, yams)
  - Legumes (beans, groundnuts, soybeans, peas)
  - Herbs & Spices (chili, ginger, garlic, turmeric)
- **Advanced Filtering**: Filter by category, price, and more
- **Search Functionality**: Search products by name
- **Shopping Cart**: Add products and manage quantities
- **Pagination**: Browse through multiple pages of products

## Technology Stack

### Frontend
- HTML5
- CSS3 (Bootstrap 5)
- JavaScript (Vanilla JS)
- Chart.js for analytics
- Font Awesome for icons

### Backend Requirements
- PHP 7.4+
- MySQL 5.7+
- Apache 2.4+
- XAMPP (recommended for development)

## Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/yourusername/AgriCool_Link.git
   ```

2. **Set Up XAMPP**
   - Install XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
   - Start Apache and MySQL services

3. **Configure the Project**
   - Copy the project files to `c:\xampp\htdocs\AgriCool_Link\`
   - Create a new MySQL database named `agricool_link`
   - Import the database schema from `database/schema.sql`

4. **Access the Application**
   - Open your web browser
   - Navigate to `http://localhost/AgriCool_Link`

## Project Structure

```
AgriCool_Link/
├── css/                    # Stylesheet files
│   ├── style.css          # Main stylesheet
│   └── marketplace.css     # Marketplace specific styles
├── js/                     # JavaScript files
│   ├── dashboard.js       # Dashboard functionality
│   └── marketplace.js     # Marketplace functionality
├── images/                 # Image assets
├── pages/                  # HTML pages
│   ├── dashboard-farmer.html
│   ├── dashboard-storage.html
│   ├── dashboard-buyer.html
│   └── marketplace.html
├── database/              # Database files
│   └── schema.sql        # Database schema
├── index.html            # Landing page
└── README.md             # Project documentation
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgments

- Bootstrap team for the amazing CSS framework
- Chart.js team for the charting library
- Font Awesome for the icons
- XAMPP team for the development environment

## Contact

For any inquiries or support, please contact:
- Email: support@agricoollink.com
- Website: www.agricoollink.com
- Phone: +260 XXX XXX XXX
