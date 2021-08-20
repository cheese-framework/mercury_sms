$("a.delete").on("click", function (e) {
  e.preventDefault();

  if (confirm("Are you sure?")) {
    var frm = $("<form>");
    frm.attr("method", "post");
    frm.attr("action", $(this).attr("href"));
    frm.appendTo("body");
    console.log(frm);
    frm.submit();
  }
});

$(document).ready(function () {
  try {
    $("table.dt").DataTable({
      pageLength: 5,
    });
  } catch (error) {
    console.log(error);
  }
});

try {
  ClassicEditor.create(document.querySelector(".editor"), {
    toolbar: [
      "Heading",
      "|",
      "bold",
      "italic",
      "link",
      "bulletedList",
      "numberedList",
      "blockQuote",
    ],
    heading: {
      options: [
        {
          model: "paragraph",
          title: "Mercury-Paragraph",
          class: "ck-heading_paragraph",
        },
        {
          model: "heading1",
          view: "h1",
          title: "Mercury-Heading-1",
          class: "ck-heading_heading1",
        },
        {
          model: "heading2",
          view: "h2",
          title: "Mercury-Heading-2",
          class: "ck-heading_heading2",
        },
        {
          model: "heading3",
          view: "h3",
          title: "Mercury-Heading-3",
          class: "ck-heading_heading3",
        },
      ],
    },
  }).catch((error) => {
    console.log(error);
  });
} catch (err) {}

function sendMail(subject, from, name, message) {
  const xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    const response = xhr.response;
    console.log(response);
  };
  xhr.open(
    "GET",
    `mailserver.php?sub=${subject}&from=${from}&name=${name}&message=${message}`,
    true
  );
  xhr.send();
}
