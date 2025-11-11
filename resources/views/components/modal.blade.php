@auth
<div id="addToListModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>📋 Aggiungi "{{ $film->title}}" alla Tua Lista</h3>
            <button onclick="closeAddToListModal()" class="modal-close">&times;</button>
        </div>
        <form id="addToListForm" onsubmit="addFilmToList({{ $film->id }}); return false;">
            @csrf
            <div class="form-group">
                <label for="status">Stato</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="plan_to_watch">📋 Da Vedere</option>
                    <option value="watching">▶️ Sto Guardando</option>
                    <option value="completed">✅ Completato</option>
                    <option value="dropped">❌ Abbandonato</option>
                </select>
            </div>
            <div class="form-group">
                <label for="rating">Voto (1-10)</label>
                <input type="number" name="rating" id="rating" class="form-control" min="1" max="10" placeholder="Opzionale">
            </div>
            <div class="form-group">
                <label for="personal_notes">Note Personali</label>
                <textarea name="personal_notes" id="personal_notes" class="form-control" rows="3" maxlength="1000" placeholder="Opzionale"></textarea>
            </div>
            <div class="modal-actions">
                <button type="submit" class="btn btn-primary">📋 Aggiungi</button>
                <button type="button" onclick="closeAddToListModal()" class="btn btn-secondary">Annulla</button>
            </div>
        </form>
    </div>
</div>


@endauth