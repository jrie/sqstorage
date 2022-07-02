<!-- The Modal -->
<div class="modal fade" id="Modal" tabindex="-1" aria-labelledby="ModalLabel" aria-modal="true"
    role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">{t}Schnelle Bearbeitung{/t}</h5>
                <button type="button" class="close" aria-label="Close" onclick="closeModal()">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form class="inputForm" accept-charset="utf-8" method="POST" action="">
            <div class="modal-body">



                <input type="hidden" value="" id="UpdateID" name="UpdateID" />
                <input type="hidden" value="" id="UpdateField" name="UpdateField" />
                <input type="hidden" value="" id="UpdateTable" name="UpdateTable" />
                <input type="hidden" value="" id="refresh" name="refresh" />

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="nvl">{t}Neuer Wert{/t}</span>
                    </div>

                    <input type="text" id="newval" name="label" maxlength="64" class="form-control" required="required" placeholder="{t}Neuer Wert{/t}")aria-label="{t}Neuer Wert{/t}" aria-describedby="nvl" value="">


                </div>



            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">{t}Abbrechen{/t}</button>
                <button type="button" class="btn btn-primary" onclick="saveModal()">{t}Speichern{/t}</button>
            </div>
            <form>
        </div>
    </div>
</div>
<div class="modal-backdrop fade show" id="backdrop" style="display: none;"></div>


