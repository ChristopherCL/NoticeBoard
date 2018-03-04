An application designed for the publication of local announcements written using Symfony
3.3 with the implemented FOSUserBundle module. Only the system administrator can
manage thematic categories. Registered users can add advertisements containing title,
description, photo and expiration date. Each advertisement can be assigned to many
categories. All application users can view active advertisements and comment on them. The
application has implemented a console command sending users notifications to the email
address about new comments for their adverts (Emails contain segregated information on the
number of comments for each announcement of each user). Twig was used to display the
templates, the database uses Doctrine.