
const images = document.querySelectorAll('.slideshow img');
        let currentIndex = 0;

        function changeImage() {
            images[currentIndex].classList.remove('active');
            currentIndex = (currentIndex + 1) % images.length;
            images[currentIndex].classList.add('active');
        }

        setInterval(changeImage, 3000);
       
        document.addEventListener('DOMContentLoaded', function() {
            // Select the sidebar and toggle button
            const sidebar = document.getElementById('sidebar');
            const toggleButton = document.createElement('button');
            toggleButton.innerHTML = '☰'; // Menu icon
            toggleButton.className = 'sidebar-toggle';
            document.body.appendChild(toggleButton);
        
            // Function to toggle the sidebar and button position
            function toggleSidebar() {
                sidebar.classList.toggle('show'); // Add or remove the 'show' class
                toggleButton.classList.toggle('move-left'); // Add or remove the 'move-left' class
            }
        
            // Event listener for the toggle button
            toggleButton.addEventListener('click', toggleSidebar);
        });
        