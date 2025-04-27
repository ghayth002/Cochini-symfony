# FitHabit Application

## Email Configuration with Mailtrap

This application uses Mailtrap for sending email notifications when an admin responds to a reclamation. To set up the email functionality properly, follow these steps:

### Setting up Mailtrap

1. Create an account on [Mailtrap](https://mailtrap.io/)
2. Log in to your Mailtrap account
3. Create a new inbox or use the default one
4. Go to the inbox settings and find the SMTP credentials
5. Create or update your `.env.local` file in the root of your project (this file is git-ignored) with the following configuration:

```
# Mailtrap configuration for Symfony Mailer
MAILER_DSN=smtp://USERNAME:PASSWORD@sandbox.smtp.mailtrap.io:2525

# Email sender address
MAILER_FROM=reclamation@yourdomain.com
```

6. Replace `USERNAME` and `PASSWORD` with your actual Mailtrap credentials
7. Update the MAILER_FROM email address if needed

### Troubleshooting Email Issues

If emails are not being sent properly, check the following:

1. Verify that your Mailtrap credentials are correct in `.env.local`
2. Check the application logs at `var/log/email.log` and `var/log/mailer.log` for detailed error messages
3. Ensure the MAILER_FROM environment variable is correctly set
4. Make sure the User entity has a valid email address for the adherent
5. Verify that Symfony Mailer is functioning correctly by checking your Mailtrap inbox

### How Email Sending Works

When an admin responds to a reclamation:

1. The response is saved in the database
2. The reclamation is marked as processed (treated)
3. An email notification is sent to the adherent who submitted the reclamation
4. The email contains both the original reclamation details and the admin's response

If the email fails to send, the application will still save the response but will display a warning message to the admin.

### Testing the Email Functionality

1. Log in as an admin
2. Go to the reclamations management section
3. Respond to a reclamation
4. Check your Mailtrap inbox to see the captured email
5. If the email doesn't appear, check the logs for error messages 