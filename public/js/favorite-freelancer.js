document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".favoriteBtn").forEach(btn => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();

            let freelancerId = this.dataset.id;
            let icon = this.querySelector("i");
            let url = `/company/freelancer/like/${freelancerId}`;

            fetch(url, {
                method: "POST",  // Laravel のルートと統一
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.favorite) {
                    icon.classList.remove("fa-regular");
                    icon.classList.add("fa-solid", "text-red-500");
                } else {
                    icon.classList.remove("fa-solid", "text-red-500");
                    icon.classList.add("fa-regular");
                }
            })
            .catch(error => console.error("Error:", error));
        });
    });
});


