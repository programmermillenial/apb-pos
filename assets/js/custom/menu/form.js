// $("#images").on("change", function () {
//   const preview = $("#image-preview");
//   preview.html("");

//   const files = this.files;

//   if (!files.length) {
//     return;
//   }

//   Array.from(files).forEach(function (file, index) {
//     if (!file.type.startsWith("image/")) {
//       return;
//     }

//     const reader = new FileReader();

//     reader.onload = function (e) {
//       const badge =
//         index === 0
//           ? `<span class="badge bg-primary position-absolute top-0 start-0 m-1">Primary</span>`
//           : "";

//       preview.append(`
//                 <div class="position-relative border rounded p-1" style="width: 120px;">
//                     ${badge}
//                     <img src="${e.target.result}"
//                          class="img-fluid rounded"
//                          style="height: 90px; width: 100%; object-fit: cover;">
//                     <small class="d-block text-truncate mt-1">${file.name}</small>
//                 </div>
//             `);
//     };

//     reader.readAsDataURL(file);
//   });
// });

document.querySelectorAll(".price-format").forEach(function (input) {
  input.addEventListener("input", function (e) {
    let value = this.value.replace(/[^0-9]/g, "");

    if (value === "") {
      this.value = "";
      return;
    }

    this.value = new Intl.NumberFormat("id-ID").format(value);
  });
});

function previewImages(input) {
  const preview = document.getElementById("image-preview");
  preview.innerHTML = "";

  if (!input.files || input.files.length === 0) {
    return;
  }

  Array.from(input.files).forEach(function (file, index) {
    if (!file.type.match("image.*")) {
      return;
    }

    const reader = new FileReader();

    reader.onload = function (e) {
      preview.innerHTML += `
                <div class="col-md-2 col-4">
                    <div class="position-relative border rounded p-1">
                        ${index === 0 ? '<span class="badge bg-primary position-absolute top-0 start-0 m-1">Primary</span>' : ""}
                        <img src="${e.target.result}"
                             style="width:100%;height:90px;object-fit:cover;"
                             class="rounded">
                        <small class="d-block text-truncate mt-1">${file.name}</small>
                    </div>
                </div>
            `;
    };

    reader.readAsDataURL(file);
  });
}

// $("#images").on("change", function () {
//   previewImages(this);
// });
