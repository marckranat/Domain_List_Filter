# 🌐 Domain List Filter

**Advanced Domain Cleanup and Comparison Tool**

A  PHP web application that cleans, organizes, and compares domain lists with intelligent subdomain grouping and comprehensive sanitization features.

![Domain List Filter Demo](https://img.shields.io/badge/PHP-8.0%2B-blue) ![License](https://img.shields.io/badge/license-MIT-green) ![Status](https://img.shields.io/badge/status-active-brightgreen)

## ✨ Features

### 🧹 Advanced Domain Cleaning
- **Protocol Removal**: Automatically strips `http://`, `https://`, `ftp://`, and other protocols
- **WWW Prefix Removal**: Removes `www.` prefixes for cleaner domain lists
- **Path & Query Removal**: Eliminates trailing slashes, paths, and query parameters
- **Port Number Removal**: Strips port numbers (e.g., `:8080`, `:443`)
- **Case Normalization**: Converts all domains to lowercase for consistency

### 🔍 Data Sanitization & Validation
- **Input Sanitization**: Filters and validates all user input
- **Domain Validation**: Ensures domains follow proper format standards
- **Duplicate Removal**: Automatically eliminates duplicate entries
- **Entry Limiting**: Restricts input to 5,000 entries per list for performance

### 📊 Intelligent Organization
- **Subdomain Grouping**: Organizes subdomains under their apex domains
- **Hierarchical Display**: Shows clear parent-child relationships
- **Alphabetical Sorting**: Automatically sorts domains and subdomains
- **Visual Indicators**: Uses icons and indentation for clarity

### 🔄 Comprehensive Comparison
- **Unique Domains**: Shows domains present only in List A
- **Unique Domains**: Shows domains present only in List B
- **Common Domains**: Displays domains found in both lists
- **Combined Results**: Merged list with all unique domains
- **Statistical Overview**: Real-time counters for each category

### 🎨 Modern Interface
- **Responsive Design**: Works seamlessly on desktop and mobile
- **Beautiful UI**: Modern gradient design with smooth animations
- **User-Friendly**: Intuitive form layout with helpful placeholders
- **Visual Feedback**: Clear error messages and success indicators

## 🚀 Quick Start

### Requirements
- PHP 7.4 or higher
- Web server (Apache, Nginx, etc.)
- No additional dependencies required

### Installation

1. **Clone the repository:**
```bash
git clone https://github.com/marckranat/Domain_List_Filter.git
cd Domain_List_Filter
```

2. **Upload to your web server:**
```bash
# Copy files to your web directory
cp Domain_List_Filter.php /var/www/html/
```

3. **Access the application:**
```
http://your-domain.com/Domain_List_Filter.php
```

## 📖 Usage

### Basic Usage
1. **Input Domain Lists**: Paste your domain lists into the two text areas
2. **Click Filter & Compare**: Process and compare your lists
3. **Review Results**: Examine the organized output with statistics

### Input Format
Enter domains one per line in any of these formats:
```
example.com
https://www.subdomain.example.com/
http://another-domain.org:8080/path
ftp://files.example.net
```

### Example Input
```
https://www.google.com/
subdomain.google.com
http://facebook.com/
www.facebook.com
instagram.com/photos
```

### Example Output
```
📊 Combined Lists (3 unique domains)

facebook.com

google.com
subdomain.google.com

instagram.com
```

## 🛠️ Technical Details

### Architecture
- **Object-Oriented Design**: Clean, maintainable class structure
- **Separation of Concerns**: Logic separated from presentation
- **Error Handling**: Comprehensive exception handling
- **Security**: Input sanitization and validation

### Key Methods
- `cleanDomain()`: Normalizes and validates individual domains
- `processDomainList()`: Processes entire domain lists
- `groupByApexDomain()`: Organizes subdomains under apex domains
- `compareDomainLists()`: Performs comprehensive list comparison

### Performance Considerations
- **Entry Limiting**: Maximum 5,000 entries per list
- **Efficient Processing**: Optimized algorithms for large datasets
- **Memory Management**: Careful handling of large arrays
- **Response Time**: Fast processing even with maximum entries

## 🎯 Use Cases

### Digital Marketing
- **SEO Analysis**: Compare competitor domain lists
- **Link Building**: Organize potential link targets
- **Domain Research**: Clean and categorize domain inventories

### Security & Compliance
- **Blocklist Management**: Organize and compare domain blocklists
- **Whitelist Validation**: Ensure proper domain formatting
- **Policy Enforcement**: Compare allowed vs. blocked domains

### Web Development
- **Migration Planning**: Compare old vs. new domain structures
- **Configuration Management**: Organize domain settings
- **Quality Assurance**: Validate domain configurations

## 📋 Changelog

### Version 2.0 (Current)
- ✅ Complete rewrite with object-oriented architecture
- ✅ Advanced domain cleaning with protocol removal
- ✅ Intelligent subdomain grouping
- ✅ Modern responsive UI with animations
- ✅ Enhanced data sanitization
- ✅ 5,000 entry limit for performance
- ✅ Comprehensive error handling
- ✅ Statistical overview dashboard

### Version 1.0
- ✅ Basic domain comparison functionality
- ✅ WWW prefix removal
- ✅ Simple HTML interface

## 🤝 Contributing

We welcome contributions! Please follow these steps:

1. **Fork the repository**
2. **Create a feature branch**: `git checkout -b feature/amazing-feature`
3. **Make your changes**: Implement your improvements
4. **Test thoroughly**: Ensure all functionality works
5. **Commit your changes**: `git commit -m 'Add amazing feature'`
6. **Push to the branch**: `git push origin feature/amazing-feature`
7. **Create a Pull Request**: Submit your changes for review

### Development Guidelines
- Follow PSR-12 coding standards
- Add comments for complex logic
- Test with various domain formats
- Ensure responsive design compatibility

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Thanks to all contributors who helped improve this tool
- Inspired by the need for better domain management tools
- Built with modern PHP best practices

## 📞 Support

Having issues? Here's how to get help:

1. **Check the Documentation**: Review this README thoroughly
2. **Search Issues**: Look through existing GitHub issues
3. **Create an Issue**: Submit a detailed bug report or feature request
4. **Community Support**: Engage with other users in discussions

## 🔗 Links

- **Repository**: https://github.com/marckranat/Domain_List_Filter
- **Issues**: https://github.com/marckranat/Domain_List_Filter/issues
- **Releases**: https://github.com/marckranat/Domain_List_Filter/releases

---

**Made with ❤️ for the web development community**

*Last updated: 2024* 