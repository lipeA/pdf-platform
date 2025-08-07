// assets/js/script.js
$(document).ready(function () {
  // Adicionar nova seção
  $("#formAddSecao").on("submit", function (e) {
    e.preventDefault();
    const formData = $(this).serialize();

    $.post(
      "includes/functions.php?action=add_secao",
      formData,
      function (response) {
        if (response.success) {
          location.reload();
        } else {
          alert("Erro ao adicionar seção: " + response.message);
        }
      }
    ).fail(function () {
      alert("Erro na requisição.");
    });
  });

  // Configurar Sortable para arrastar e soltar
  $(".arquivos-sortable").each(function () {
    const secaoId = $(this).closest(".arquivos-list").data("secao-id");
    new Sortable(this, {
      animation: 150,
      ghostClass: "sortable-ghost",
      onEnd: function () {
        const newOrder = [];
        $(this.el)
          .find(".arquivo-item")
          .each(function (index) {
            newOrder.push($(this).data("arquivo-id"));
          });

        $.post(
          "reorder.php",
          {
            id_secao: secaoId,
            new_order: JSON.stringify(newOrder),
          },
          function (response) {
            if (!response.success) {
              alert("Erro ao salvar a nova ordem.");
            }
          }
        );
      },
    });
  });

  // Renomear seção
  $(".rename-secao").on("click", function () {
    const secaoId = $(this).data("secao-id");
    const secaoNome = $(this)
      .closest(".card-header")
      .find(".secao-titulo")
      .text();

    $("#renameModalTitle").text("Renomear Seção");
    $("#renameItemType").val("secao");
    $("#renameItemId").val(secaoId);
    $("#newName").val(secaoNome);
    $("#renameModal").modal("show");
  });

  // Renomear arquivo
  $(document).on("click", ".rename-arquivo", function () {
    const arquivoId = $(this).data("arquivo-id");
    const arquivoNome = $(this)
      .closest(".arquivo-item")
      .find(".arquivo-nome")
      .text();

    $("#renameModalTitle").text("Renomear Arquivo");
    $("#renameItemType").val("arquivo");
    $("#renameItemId").val(arquivoId);
    $("#newName").val(arquivoNome);
    $("#renameModal").modal("show");
  });

  // Confirmar renomeação
  $("#confirmRename").on("click", function () {
    const itemType = $("#renameItemType").val();
    const itemId = $("#renameItemId").val();
    const newName = $("#newName").val();

    if (!newName.trim()) {
      alert("Por favor, insira um nome válido.");
      return;
    }

    $.post(
      "rename.php",
      {
        [itemType === "secao" ? "id_secao" : "id_arquivo"]: itemId,
        novo_nome: newName,
      },
      function (response) {
        if (response.success) {
          location.reload();
        } else {
          alert("Erro ao renomear: " + response.message);
        }
      }
    );
  });

  // Excluir arquivo
  $(document).on("click", ".delete-arquivo", function () {
    if (!confirm("Tem certeza que deseja excluir este arquivo?")) {
      return;
    }

    const arquivoId = $(this).data("arquivo-id");

    $.post(
      "delete.php",
      {
        id_arquivo: arquivoId,
      },
      function (response) {
        if (response.success) {
          location.reload();
        } else {
          alert("Erro ao excluir o arquivo: " + response.message);
        }
      }
    );
  });

  // Upload de arquivo
  $(".form-upload").on("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    $.ajax({
      url: "upload.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        if (response.success) {
          location.reload();
        } else {
          alert("Erro no upload: " + (response.message || "Erro desconhecido"));
        }
      },
      error: function () {
        alert("Erro na requisição.");
      },
    });
  });
});
