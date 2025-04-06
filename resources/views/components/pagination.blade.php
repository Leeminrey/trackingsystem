 <!-- Pagination Controls -->
            <div class="pagination">
                <button class="prev" onclick="changePage(-1)">❮ Prev</button>
                <span id="page-info">Page 1 of {{ ceil($documents->count() / 5) }}</span>
                <button class="next" onclick="changePage(1)">Next ❯</button>
            </div>

            