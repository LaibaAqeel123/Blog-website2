-- Create database
CREATE DATABASE IF NOT EXISTS blog_db;
USE blog_db;

-- Admin table (WITHOUT password hashing)
CREATE TABLE IF NOT EXISTS admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Posts table
CREATE TABLE IF NOT EXISTS posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    category_id INT,
    author VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Tags table
CREATE TABLE IF NOT EXISTS tags (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Post-Tags relationship table
CREATE TABLE IF NOT EXISTS post_tags (
    post_id INT,
    tag_id INT,
    PRIMARY KEY (post_id, tag_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

-- Comments table
CREATE TABLE IF NOT EXISTS comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

-- Insert default admin user (PLAIN TEXT PASSWORD)
-- Username: admin
-- Password: admin123 (stored as plain text)
INSERT INTO admin (username, password) VALUES 
('admin', 'admin123');

-- Insert sample categories
INSERT INTO categories (name) VALUES 
('Technology'),
('Lifestyle'),
('Travel'),
('Food'),
('Health'),
('Business');

-- Insert sample tags
INSERT INTO tags (name) VALUES 
('PHP'),
('JavaScript'),
('Tutorial'),
('News'),
('Tips'),
('Guide'),
('Review'),
('Opinion');

-- Insert sample posts
INSERT INTO posts (title, content, category_id, author) VALUES 
(
    'Getting Started with PHP Development',
    'PHP is a powerful server-side scripting language that is widely used for web development. In this comprehensive guide, we will explore the fundamentals of PHP programming and how to build dynamic websites.

PHP stands for Hypertext Preprocessor and is an open-source scripting language. It is particularly suited for web development and can be embedded into HTML. PHP scripts are executed on the server, and the result is returned to the browser as plain HTML.

One of the key advantages of PHP is its ease of use. Even beginners can quickly start writing PHP code and see results. The language has a simple syntax that is easy to understand, making it an excellent choice for those new to programming.

PHP is also incredibly versatile. You can use it to create everything from simple contact forms to complex web applications and content management systems. Popular platforms like WordPress, Drupal, and Joomla are all built with PHP.

To get started with PHP, you will need a web server with PHP support and a text editor. Many developers use XAMPP or WAMP, which provide an easy-to-install Apache distribution containing PHP and MySQL.

Understanding basic PHP syntax, variables, operators, control structures, and functions is essential. As you progress, you will learn about more advanced topics like object-oriented programming, database integration with MySQL, and security best practices.',
    1,
    'John Developer'
),
(
    'The Ultimate Guide to Healthy Living',
    'Living a healthy lifestyle is more important than ever in today modern world. This comprehensive guide will walk you through the essential steps to improve your overall health and wellbeing.

Nutrition is the foundation of good health. Eating a balanced diet rich in fruits, vegetables, whole grains, lean proteins, and healthy fats provides your body with the nutrients it needs to function optimally. Try to minimize processed foods, added sugars, and excessive salt intake.

Regular physical activity is crucial for maintaining a healthy body and mind. Aim for at least 150 minutes of moderate-intensity aerobic exercise per week, along with strength training exercises twice a week. Find activities you enjoy, whether it is walking, swimming, cycling, or dancing.

Sleep is often overlooked but is vital for health. Most adults need 7-9 hours of quality sleep each night. Establish a consistent sleep schedule and create a relaxing bedtime routine to improve your sleep quality.

Stress management is another critical component of healthy living. Chronic stress can negatively impact your physical and mental health. Practice relaxation techniques like meditation, deep breathing, or yoga to help manage stress levels.

Stay hydrated by drinking plenty of water throughout the day. Water is essential for nearly every bodily function, from regulating body temperature to flushing out toxins.

Regular health check-ups and screenings are important for early detection and prevention of health issues. Do not neglect your mental health either seek support when needed and maintain strong social connections.',
    5,
    'Dr. Sarah Health'
),
(
    'Top 10 Travel Destinations for 2025',
    'As we look forward to new adventures, here are the top 10 travel destinations that should be on your bucket list for 2025. These locations offer unique experiences, stunning landscapes, and unforgettable memories.

1. Iceland - Known for its dramatic landscapes, including waterfalls, geysers, hot springs, and lava fields. The Northern Lights are a spectacular sight during winter months.

2. New Zealand - A paradise for adventure seekers and nature lovers, offering everything from bungee jumping to serene fjords and pristine beaches.

3. Japan - A perfect blend of ancient traditions and modern technology. Experience cherry blossoms, historic temples, bustling cities, and incredible cuisine.

4. Portugal - Beautiful coastal towns, historic cities like Lisbon and Porto, delicious food, and friendly locals make Portugal an ideal destination.

5. Costa Rica - A biodiverse paradise with rainforests, beaches, volcanoes, and abundant wildlife. Perfect for eco-tourism and adventure activities.

6. Morocco - Exotic markets, stunning architecture, the Sahara Desert, and rich cultural heritage await in this North African gem.

7. Peru - Home to Machu Picchu, the Amazon rainforest, and fascinating Incan history. A must-visit for history and nature enthusiasts.

8. Croatia - Crystal-clear Adriatic waters, medieval cities, beautiful islands, and Game of Thrones filming locations.

9. Vietnam - From the bustling streets of Hanoi to the serene waters of Ha Long Bay, Vietnam offers diverse experiences and incredible food.

10. Norway - Fjords, Northern Lights, midnight sun, and stunning Scandinavian landscapes make Norway a bucket-list destination.

Each destination offers unique experiences that cater to different interests and travel styles. Start planning your next adventure today!',
    3,
    'Emma Wanderlust'
),
(
    'Web Development Trends in 2025',
    'The world of web development is constantly evolving, with new technologies and trends emerging regularly. Here are the key trends shaping web development in 2025.

Progressive Web Apps (PWAs) continue to gain popularity. They combine the best features of web and mobile apps, offering offline functionality, push notifications, and app-like experiences through web browsers.

Artificial Intelligence and Machine Learning integration is becoming more prevalent. AI-powered chatbots, personalized content recommendations, and intelligent search functionality are enhancing user experiences.

Serverless architecture is simplifying backend development. Developers can focus on writing code without worrying about server management, leading to faster deployment and reduced costs.

Motion UI and micro-interactions are adding life to websites. Subtle animations and interactive elements improve user engagement and make interfaces more intuitive and enjoyable.

Voice search optimization is crucial as more users rely on voice assistants. Websites need to be optimized for natural language queries and conversational search.

Cybersecurity remains a top priority. With increasing data breaches and privacy concerns, implementing robust security measures is essential for protecting user data.

Dark mode design has become standard. Many websites now offer dark mode options to reduce eye strain and improve readability in low-light conditions.

Responsive design has evolved to include not just mobile devices but also smartwatches, IoT devices, and foldable screens. Developers must consider a wider range of screen sizes and device types.

The JAMstack architecture (JavaScript, APIs, and Markup) is gaining traction for building fast, secure, and scalable websites.

Staying updated with these trends is crucial for web developers to create modern, efficient, and user-friendly websites.',
    1,
    'Michael Tech'
),
(
    'Delicious Mediterranean Recipes',
    'Mediterranean cuisine is renowned for its health benefits, fresh ingredients, and incredible flavors. Here are some delicious recipes that bring the taste of the Mediterranean to your kitchen.

Greek Salad (Horiatiki)
This classic Greek salad is simple yet delicious. Combine fresh tomatoes, cucumbers, red onions, Kalamata olives, and feta cheese. Dress with extra virgin olive oil, red wine vinegar, dried oregano, salt, and pepper. Serve immediately for the freshest taste.

Spanish Paella
This iconic Spanish rice dish is perfect for gatherings. Sauté onions, garlic, and bell peppers in olive oil. Add Arborio rice, saffron, and vegetable or chicken broth. Layer with seafood, chicken, and vegetables. Cook until rice is tender and has absorbed the flavorful broth.

Italian Caprese Salad
Layer fresh mozzarella, ripe tomatoes, and basil leaves. Drizzle with balsamic glaze and olive oil. Season with salt and pepper. This simple dish showcases the quality of its ingredients.

Turkish Hummus
Blend chickpeas, tahini, lemon juice, garlic, and cumin until smooth. Add olive oil while blending for a creamy texture. Serve with warm pita bread and fresh vegetables.

Moroccan Tagine
This slow-cooked stew combines meat or vegetables with aromatic spices like cumin, coriander, cinnamon, and turmeric. Add dried fruits for sweetness and serve over couscous.

The Mediterranean diet emphasizes whole grains, fresh fruits and vegetables, lean proteins, and healthy fats like olive oil. These recipes not only taste amazing but also contribute to a healthy lifestyle.

Cooking Mediterranean food at home allows you to control ingredients and customize dishes to your preferences. Fresh herbs, quality olive oil, and seasonal produce are key to authentic flavors.',
    4,
    'Chef Antonio'
),
(
    'Building Your First Mobile App',
    'Creating a mobile application might seem daunting, but with the right approach and tools, anyone can build their first app. This guide will walk you through the essential steps.

Step 1: Define Your Idea
Start with a clear vision of what problem your app will solve. Research similar apps to understand the market and identify gaps your app can fill. Define your target audience and unique value proposition.

Step 2: Choose Your Development Approach
Decide between native development (iOS or Android specific), cross-platform frameworks (React Native, Flutter), or hybrid approaches. Each has advantages and trade-offs in terms of performance, development time, and cost.

Step 3: Design Your User Interface
Create wireframes and mockups of your app screens. Focus on user experience (UX) and ensure navigation is intuitive. Tools like Figma, Sketch, or Adobe XD can help visualize your design.

Step 4: Set Up Development Environment
Install necessary software development kits (SDKs), integrated development environments (IDEs), and tools. For iOS, you will need Xcode on a Mac. For Android, Android Studio works on multiple platforms.

Step 5: Start Coding
Begin with core functionality before adding advanced features. Write clean, maintainable code and follow best practices for your chosen platform or framework.

Step 6: Test Thoroughly
Test your app on various devices and screen sizes. Identify and fix bugs. Consider beta testing with real users to gather feedback.

Step 7: Prepare for Launch
Create app store listings with compelling descriptions and screenshots. Follow platform-specific guidelines for submission to Apple App Store or Google Play Store.

Step 8: Market Your App
Develop a marketing strategy to reach your target audience. Use social media, content marketing, and possibly paid advertising to promote your app.

Step 9: Maintain and Update
After launch, monitor user feedback and analytics. Release updates to fix issues, add features, and improve performance.

Building your first mobile app is a learning experience. Start small, be patient, and do not be afraid to iterate based on user feedback.',
    1,
    'David Mobile'
);

-- Link posts with tags
INSERT INTO post_tags (post_id, tag_id) VALUES
(1, 1), (1, 3), -- PHP, Tutorial
(2, 5), (2, 6), -- Tips, Guide
(3, 7), (3, 5), -- Review, Tips
(4, 2), (4, 3), (4, 4), -- JavaScript, Tutorial, News
(5, 5), (5, 6), -- Tips, Guide
(6, 3), (6, 6); -- Tutorial, Guide

-- Insert sample comments
INSERT INTO comments (post_id, name, email, comment) VALUES
(1, 'Alice Johnson', 'alice@example.com', 'Great tutorial! Very helpful for beginners like me. I especially liked the step-by-step approach.'),
(1, 'Bob Smith', 'bob@example.com', 'Thanks for sharing this. Do you have any recommendations for PHP frameworks to learn next?'),
(2, 'Carol White', 'carol@example.com', 'Excellent article! I have already started implementing some of these tips in my daily routine.'),
(3, 'David Brown', 'david@example.com', 'Iceland has been on my bucket list for years! This article convinced me to finally book a trip.'),
(4, 'Eva Martinez', 'eva@example.com', 'Very informative post about web development trends. AI integration is indeed becoming crucial.'),
(5, 'Frank Lee', 'frank@example.com', 'Tried the Greek salad recipe last night - absolutely delicious! Thank you for sharing.');

-- Success message
SELECT 'Database setup completed successfully! Admin password is stored as plain text.' AS message;