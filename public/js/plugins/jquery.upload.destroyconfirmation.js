$.widget('blueimpUIX.fileupload', $.blueimpUI.fileupload, {
    options: {
        deleteall: false,
        destroy: function (e, data) {
            var me = this;

            if (e.data.fileupload.options.deleteall) {
                // delete all
                $.blueimpUI.fileupload.prototype.options.destroy.call(this, e, data);
            } else {
                // confirm single deletion
                $("#dialog-delete").dialog({
                    resizable: false,
                    height: 170,
                    modal: true,
                    title: 'Löschen der Datei',
                    buttons: [
                              {
                                  text: 'Abbruch',
                                  click: function() {
                                      $(this).dialog("close");
                                  } 
                              },
                              {
                                  text: 'Löschen',
                                  click: function() {
                                      $(this).dialog("close");
                                      $.blueimpUI.fileupload.prototype.options.destroy.call(me, e, data);
                                  } 
                              }
                              ],
                              open: function() {
                                  $(this).find("p").html("Soll die ausgewählte Datei wirklich gelöscht werden?");
                              }
                });
            }
        }
    },

    _initButtonBarEventHandlers: function () {
        // set default handler 
        $.blueimpUI.fileupload.prototype._initButtonBarEventHandlers.call(this);

        // vars
        var fileUploadButtonBar = this.element.find('.fileupload-buttonbar'),
        filesList = this.options.filesContainer,
        ns = this.options.namespace,
        me = this;

        // unbind click event
        fileUploadButtonBar.find('.delete').unbind('click.' + ns);

        // bind new click event
        fileUploadButtonBar.find('.delete').bind('click.' + ns, function (e) {
            e.preventDefault();
            if (filesList.find('.delete input:checked').length > 0) {
                // confirm all deletion
                $("#dialog-delete").dialog({
                    resizable: false,
                    height: 190,
                    modal: true,
                    title: 'Löschen der ausgewählten Dateien',
                    buttons: [
                              {
                                  text: 'Abbruch',
                                  click: function() {
                                      $(this).dialog("close");
                                  } 
                              },
                              {
                                  text: 'Löschen',
                                  click: function() {
                                      $(this).dialog("close");

                                      me.options.deleteall = true;
                                      filesList.find('.delete input:checked').siblings('button').click();
                                      fileUploadButtonBar.find('.toggle').prop('checked', false);
                                      me.options.deleteall = false;
                                  } 
                              }
                              ],
                              open: function() {
                                  $(this).find("p").html("Sollen die ausgewählte Datei wirklich alle gelöscht werden?");
                              }
                });
            }
        });
    }
});