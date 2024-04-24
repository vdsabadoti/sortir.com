# Sortir.com
Sortir.com is a web application built with PHP and Symfony framework, designed for students and alumni from ENI. It facilitates the organization and participation in private events and activities.

## Installation
#### Clone the repository:
git clone https://github.com/your-username/sortir.com.git
#### Install dependencies:
composer install
#### Set up the database:
symfony console doctrine:database:create
symfony console doctrine:migrations:migrate
#### Start the Symfony server:
symfony server:start
### Access the application:
Open your web browser and navigate to http://localhost:8000
## Usage
#### Registration and Membership Approval:
Students and alumni from ENI can register for an account.
Admins can approve membership requests.
#### Creating Events:
Authenticated users can create private events/activities.
Specify event details such as date, time, location, and description.
#### Subscribing to Events:
Users can subscribe to events they're interested in attending.
### Contributing
Contributions are welcome! Please follow these steps to contribute:
#### Fork the repository.
Create a new branch (git checkout -b feature/my-feature).
#### Make your changes.
Commit your changes (git commit -am 'Add my feature').
#### Push to the branch (git push origin feature/my-feature).
Create a new Pull Request.
