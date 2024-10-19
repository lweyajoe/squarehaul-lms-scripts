// Create the popup container
const popup = document.createElement('div');
popup.style.position = 'fixed';
popup.style.bottom = '20px';
popup.style.right = '20px';
popup.style.width = '300px';
popup.style.height = '300px'; // Set a fixed height
popup.style.overflow = 'hidden'; // Hide overflow for the main container
popup.style.padding = '20px';
popup.style.backgroundColor = 'white';
popup.style.border = '1px solid #ccc';
popup.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
popup.style.zIndex = '1000';

// Create the close button
const closeButton = document.createElement('button');
closeButton.innerHTML = '&times;';
closeButton.style.position = 'absolute';
closeButton.style.top = '10px';
closeButton.style.right = '10px';
closeButton.style.backgroundColor = 'transparent';
closeButton.style.border = 'none';
closeButton.style.fontSize = '20px';
closeButton.style.cursor = 'pointer';

// Add event listener to close button
closeButton.addEventListener('click', function() {
    popup.style.display = 'none';
});

// Append the close button to the popup
popup.appendChild(closeButton);

// Create a scrollable content container
const contentContainer = document.createElement('div');
contentContainer.style.height = '100%';
contentContainer.style.overflowY = 'auto';
contentContainer.style.paddingRight = '10px'; // To avoid overlap with scrollbar

// Add the content to the content container
contentContainer.innerHTML = `
    <h3>User Details and Passwords</h3>
    <p>admin@admin.com : Admin001!</p>
    <p>manager@manager.com : mlcMtIFRGX</p>
    <p>client@client.com : PJLzgMqRn2</p>
    <p>client2@client.com : e6aDKAfB0m</p>
    <br>
    <p><strong>NB:</strong></p>
    <p>1. You can also create your own manager and client profiles to test the app, but cannot create an admin profile as that is super-coded in the app.</p>
    <p>2. You can test the various views and authorisations of the different users. For example, only a manager or admin can onboard a client, only admin can onboard manager, and the various users can see their reports as required.</p>
    <p>3. Some pages like about page, terms and conditions and homepage editor are built on order as they are customised.</p>
    <p>4. Pages showing 404, 403 etc errors will be built on order of the app.</p>
    <p>Thanks.</p>
`;

// Append the content container to the popup
popup.appendChild(contentContainer);

// Append the popup to the body
document.body.appendChild(popup);
