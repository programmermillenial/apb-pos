$(document).ready(function () {
  function generateSlug(text) {
    return text
      .toLowerCase()
      .replace(/[^a-z0-9\s-]/g, "")
      .replace(/\s+/g, "-")
      .replace(/-+/g, "-");
  }

  $('input[name="name"]').on("keyup", function () {
    let slug = generateSlug($(this).val());

    $('input[name="slug"]').val(slug);
  });
});
