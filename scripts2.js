

// Get references to the modal and buttons
const modal = document.getElementById("myModal");
const openModalButton = document.getElementById("openModalButton");
const closeModalButton = document.getElementById("closeModalButton");
const submitButton = document.getElementById("submit");
const successAnimation = document.getElementById('success-animation');

// Show the modal when the button is clicked
openModalButton.addEventListener("click", () => {
    modal.style.display = "block";
});

// Close the modal when the close button is clicked
closeModalButton.addEventListener("click", () => {
    modal.style.display = "none";
});

// Prevent clicks outside of the modal from closing it
modal.addEventListener("click", (event) => {
    if (event.target !== modal) {
        event.stopPropagation(); // Prevent the event from bubbling up
    }
});

submitButton.addEventListener("click",(event) => {
    successAnimation.style.display = 'block';
  setTimeout(() => {
    modal.style.display = "none";
    successAnimation.style.display = 'none';
    // Redirect to the "Add New Entry" page or update the UI as needed
  }, 4000);
});


// Get references to the date and time elements
const entryDateElement = document.getElementById("entryDate");
const entryTimeElement = document.getElementById("entryTime");
const doctorNameInput = document.getElementById("doctorName");
const hospitalNameInput = document.getElementById("hospitalName");
const fileInput = document.getElementById("docpicker");
const textField = document.getElementById("textfield");

// Function to format the date and time
function formatDateTime() {
    const now = new Date();
    const date = now.toLocaleDateString();
    const time = now.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
    entryDateElement.textContent = date;
    entryTimeElement.textContent = time;
}

formatDateTime();

// Function to display selected files with removal buttons
function displaySelectedFiles(input) {
    const fileList = document.getElementById("fileList");

    for (let i = 0; i < input.files.length; i++) {
        const fileName = input.files[i].name;
        const fileItem = document.createElement("div");

        // Create a span for the file name
        const fileNameSpan = document.createElement("span");
        fileNameSpan.textContent = fileName;

        // Create a button for file removal
        const removeButton = document.createElement("button");
        removeButton.textContent = "X";
        removeButton.classList.add("remove-button");

        // Add a click event listener to remove the file
        removeButton.addEventListener("click", () => {
            input.value = ""; // Clear the selected file
            fileItem.remove(); // Remove the file item from the list
        });

        // Append the file name and removal button to the file item
        fileItem.appendChild(fileNameSpan);
        fileItem.appendChild(removeButton);

        // Append the file item to the file list
        fileList.appendChild(fileItem);
    }
}




/*const scriptURL = 'https://script.google.com/macros/s/AKfycbyjQlkWHISQDptvoj3_U46JCv8R7jNKLYyBQ8DMhAkBKnOZ9NWz90V_KVI7ctA79ng/exec'
const form = document.forms['form-data']

form.addEventListener('submit',e => {
    e.preventDefault()
    fetch(scriptURL, {method : 'post', body : new FormData(form)})
    .then(response => alert("Thank you! Data is Submitted"))
    .then(() => { window.location.reload(); })
    .catch(error => console.error('Error!', error.message))
})
*/
/*
const scriptURL = 'https://script.google.com/macros/s/AKfycbyjQlkWHISQDptvoj3_U46JCv8R7jNKLYyBQ8DMhAkBKnOZ9NWz90V_KVI7ctA79ng/exec';
const form = document.forms['form-data'];

form.addEventListener('submit', e => {
    e.preventDefault();

    fetch(scriptURL, { method: 'post', body: new FormData(form) })
        .then(response => response.json()) // Assuming the server sends JSON response
        .then(data => {
            if (data.result === 'success') {
                alert("Thank you! Data is Submitted");
                window.location.reload();
                modal.style.display = "none";
            } else {
                alert("Error! Data submission failed");
            }
        })
        .catch(error => console.error('Error!', error.message));
});



const scriptURL = 'https://script.google.com/macros/s/AKfycbyjQlkWHISQDptvoj3_U46JCv8R7jNKLYyBQ8DMhAkBKnOZ9NWz90V_KVI7ctA79ng/exec';
const form = document.forms['form-data'];
//const modal = document.getElementById("myModal"); // Get a reference to your modal

form.addEventListener('submit', e => {
    e.preventDefault();
    fetch(scriptURL, { method: 'post', body: new FormData(form) })
        .then(response => response.json())
        .then(data => {
            console.log("Response from server:", data); // Add this line for debugging
            if (data.result === 'success') {
                // Display the "Thanks" message in the modal
                modal.textContent = "Thanks for submitting!";
                modal.style.display = "block";
                setTimeout(() => {
                    modal.style.display = "none"; // Hide the modal after a delay
                }, 2000); // Adjust the delay as needed
                form.reset(); // Optionally reset the form fields
            } else {
                alert("Error! Data submission failed");
            }
        })
        .catch(error => console.error('Error!', error.message));
});

*/

// Function to clear the form inputs
function clearForm() {
    doctorNameInput.value = "";
    hospitalNameInput.value = "";
    fileInput.value = "";
    textField.value = "";
}
