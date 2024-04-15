## Installation

1. Download `Node.js`

2. Install `Composer` dependencies:
    composer install

3. Install `NPM` dependencies:
    npm install

4. The below command will compile all the assets(sass, js, media)  to public folder:   
    npm run dev

5. Copy `.env.example` file and duplicate as `.env`

6. Migrate database structure: 
    php artisan migrate

7. Run Schedule (useful in laravel notification): 
    php artisan schedule:run

8. Start the server: 
    php artisan serve