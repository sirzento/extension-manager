# Blueprint Extension Manager
Easily install or remove your extensions directly from the panel.

![Extension Manager Overview](https://i.imgur.com/kbDf24r.png)

## How does it work?
The Extension Manager will open a local SSH connection to securely run Blueprint commands to install or remove extensions. This requires to input the password for a SSH user on each action. **The password will never be stored inside this extension!**

## Why do I need to provide a password?
There are several reasons why a password or SSH key is required for authentication. One important reason is security: Pterodactyl administrators have the ability to escalate their privileges and potentially install malicious extensions that could execute code directly on the server. By requiring authentication, even if someone gains access to your Pterodactyl admin account, they won't be able to exploit this mechanism without your credentials.

Another reason is related to system permissions. The user that runs the PHP process does not have the necessary privileges to execute Blueprint commands for managing extensions — and for security reasons, we don't want the PHP user to have that level of access.

## Getting started
Configure the SSH user you want to use for managing extension that will run the Blueprint commands via the settings button on the top left.
