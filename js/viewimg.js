async function loadGalleryImages() {
  try {
    const response = await fetch("https://api-gallery.giaonline.org/images");
    const result = await response.json();

    const container = document.getElementById("galleryContainer");
    container.innerHTML = "";

    const baseUrl = "https://api-gallery.giaonline.org/uploads/";

    result.data.forEach((item) => {
      const col = document.createElement("div");
      col.className = "col-lg-3 col-md-3 col-sm-6 mb-3";

      col.innerHTML = `
          <div class="gallery-card position-relative overflow-hidden">
            <img src="${baseUrl}${item.image}" class="img-fluid popup-image" alt="Event Image" style="cursor: zoom-in;" />
          </div>
        `;

      container.appendChild(col);
    });

    bindPopupEvents();
  } catch (err) {
    console.error("Error loading gallery images:", err);
  }
}

function bindPopupEvents() {
  const popup = document.getElementById("imagePopup");
  const popupImg = document.getElementById("popupImg");
  const closeBtn = document.getElementById("closePopup");

  document.querySelectorAll(".popup-image").forEach((img) => {
    img.addEventListener("click", () => {
      popupImg.src = img.src;
      popup.style.display = "flex";
    });
  });

  closeBtn.onclick = () => {
    popup.style.display = "none";
    popupImg.src = "";
  };

  popup.addEventListener("click", (e) => {
    if (e.target === popup) {
      popup.style.display = "none";
      popupImg.src = "";
    }
  });
}

// Load on page ready
document.addEventListener("DOMContentLoaded", loadGalleryImages);
