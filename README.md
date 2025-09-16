# PHP/Laravel Skills Test

This is a single-page application created for a skills test. It allows users to manage a list of products, with data being saved to a JSON file on the server. The entire interface is dynamic and uses AJAX for all operations, meaning no page reloads are necessary.

## Features

* **Add Products:** A form to add new products with a name, quantity, and price.
* **View Products:** A real-time table that displays all submitted products.
* **Edit Products:** Functionality to edit each product's details in a popup modal.
* **Live Data Calculation:** The table automatically calculates the total value for each item and a grand total for all items.
* **Dynamic Updates:** The product list updates instantly after adding or editing a product, thanks to AJAX.
* **Persistent Storage:** All product data is stored in a `products.json` file in the `storage/app` directory.

## Tech Stack

* **Backend:** PHP, Laravel
* **Frontend:** HTML, CSS, JavaScript, jQuery, Bootstrap 5
* **Development Environment:** Laravel Herd
* **Data Storage:** JSON
