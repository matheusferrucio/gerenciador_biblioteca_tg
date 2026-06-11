            </div><!-- /.content-area -->
        </main>
    </div><!-- /.app-wrapper -->

    <!-- ── Modal Prorrogar (Global) ── -->
    <div class="modal-overlay" id="extendModal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3>Prorrogar Empréstimo</h3>
                <button type="button" class="modal-close">&times;</button>
            </div>
            <form action="<?= BASE_URL ?>/loans/extend" method="POST" id="extendForm">
                <input type="hidden" name="loan_id" id="extendLoanId">
                <div class="modal-body">
                    <p style="font-size: 14px; margin-bottom: 15px; color: var(--gray-600);">
                        Livro: <strong id="extendBookTitle"></strong>
                    </p>
                    
                    <div class="form-group">
                        <label for="new_due_date">Nova Data de Devolução</label>
                        <input type="date" name="new_due_date" id="new_due_date" required class="form-control">
                        
                        <div class="quick-extend-group">
                            <button type="button" class="btn-extend-opt" data-days="3">+3 dias</button>
                            <button type="button" class="btn-extend-opt" data-days="7">+7 dias</button>
                            <button type="button" class="btn-extend-opt" data-days="10">+10 dias</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-close-btn">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Confirmar Prorrogação</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ── Modal Confirmar Devolução (Global) ── -->
    <div class="modal-overlay" id="confirmReturnModal">
        <div class="modal-dialog">
            <div class="modal-header" style="background: rgba(34, 197, 94, 0.05); border-bottom-color: rgba(34, 197, 94, 0.1);">
                <h3 style="color: #166534;">Confirmar Devolução</h3>
                <button type="button" class="modal-close">&times;</button>
            </div>
            <div class="modal-body" style="text-align: center; padding: 32px 24px;">
                <div style="font-size: 48px; margin-bottom: 20px;">📚✅</div>
                <p style="font-size: 16px; color: var(--gray-700); line-height: 1.5;">
                    Deseja confirmar a devolução do livro:<br>
                    <strong id="returnBookTitle" style="color: var(--gray-900); font-size: 18px;"></strong>?
                </p>
                <p style="margin-top: 15px; font-size: 13px; color: var(--gray-500);">
                    Esta ação atualizará o status do empréstimo para "Devolvido".
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close-btn">Cancelar</button>
                <a href="#" id="confirmReturnBtn" class="btn btn-success">Confirmar Devolução</a>
            </div>
        </div>
    </div>

    <script src="<?= BASE_URL ?>/public/js/app.js"></script>
</body>
</html>
