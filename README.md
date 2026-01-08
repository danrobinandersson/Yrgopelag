# Yrgopelag

## Project description

This project is a hotel booking web application built around a fictional island and hotel concept.
The chosen concept is a tented camp on a volcano island.
The website allows visitors to view room availability and book one of three single rooms—budget, standard, or luxury.
The user can also select optional features and a package deal.

The project operates together with a Central Bank service, which means the booking flow is the following:

- The user checks the dates availability and select a tent type, dates of departure and arrival, plus optional features.
- The UI displays a total amount and possible discount.
- The user enters the total amount together with the API_KEY and creates a transfercode via the Central Bank.
- If a valid transfercode is created, the user can go back to the UI and use it to proceed with the booking.
- If the booking is successful, a receipt is displayed.

Validation is in place to ensure that the user can actually book.

## Languages and project structure

Backend & Rendering: PHP (server-side rendering and business logic)

Database: SQL (booking data, rooms, features, guest stays)

Styling: CSS

Interactivity: Minimal JavaScript for UI and price display

API Communication: Guzzle HTTP client

Configuration: Environment variables for sensitive credentials

## To run the project locally

1. Clone the repository
2. Install PHP dependencies using Composer:

   ```bash
   composer install

   ```

3. Create a .env file in the project root and add your API key and database configuration

4. Run the following command to create or reset the database:
   php backend/reset-database.php

   If successful you will get the following terminal message:
   ✅ Database reset successfully: backend/database/database.db

5. Start a localhost in the terminal: php -S localhost:8000;

6. Open the site in your browser

# Limitations and future improvements

- The project is designed for desktop use only.
- The UI is one page only. In the future I could expand it and include proper navigation, routing and more data.
- The confirmation for the transfercode and receipt is very minimalistic and not styled.
- the booking flow could be improved to be more user friendly, for example the total amount could be sent directly to the central bank instead of being manually added by the user.
