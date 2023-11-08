// Wait for the DOM to be fully loaded before running the script
document.addEventListener("DOMContentLoaded", function() {
    let copyIcons = document.getElementsByClassName("clipboardIcon");
    let battlenets = document.getElementsByClassName("battlenet");

    for (let i = 0; i < copyIcons.length; i++) {
        // Add a click event listener to each "clipboardIcon" element
        copyIcons[i].addEventListener("click", function() {
            // Get the text content of the corresponding "battlenet" element
            const username = battlenets[i].textContent;

            // Create a temporary textarea element to hold text in
            const dummyTextArea = document.createElement("textarea");
            // Set its value to the username text
            dummyTextArea.value = username;
            // Append the textarea to the document body
            document.body.appendChild(dummyTextArea);
            // Select the text inside the textarea
            dummyTextArea.select();
            // Copy the selected text to the clipboard
            document.execCommand("copy");
            // Remove the temporary textarea element
            document.body.removeChild(dummyTextArea);
            // Log a message to the console indicating that the text was copied
            console.log("Copied to clipboard: " + username);
        });
    }
	



});


function reloadPageA() {
        window.location.href = 'index.php';
}