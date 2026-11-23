(() => {
    const searchInput = document.querySelector('#student-search');
    const schoolYearFilter = document.querySelector('#school-year-filter');
    const studentCards = [...document.querySelectorAll('.student-card')];
    const resultsMessage = document.querySelector('#student-search-results');

    if ((searchInput || schoolYearFilter) && resultsMessage) {
        const applyFilters = () => {
            const query = searchInput.value.trim().toLocaleLowerCase();
            const selectedSchoolYear = schoolYearFilter?.value || '';
            let visibleCount = 0;

            studentCards.forEach((card) => {
                const matchesSearch = !query
                    || card.dataset.studentSearch.toLocaleLowerCase().includes(query);
                let matchesSchoolYear = !selectedSchoolYear;

                if (selectedSchoolYear) {
                    const [startYear, endYear] = selectedSchoolYear.split('-').map(Number);
                    const entryYear = Number(card.dataset.entryYear);
                    const graduationYear = Number(card.dataset.endYear);
                    matchesSchoolYear = startYear >= entryYear && endYear <= graduationYear;
                }

                const matches = matchesSearch && matchesSchoolYear;
                card.hidden = !matches;
                if (matches) visibleCount += 1;
            });

            const filtersActive = query.length > 0 || selectedSchoolYear.length > 0;
            resultsMessage.hidden = !filtersActive;
            resultsMessage.textContent = visibleCount === 0
                ? 'No students match the selected filters.'
                : `Showing ${visibleCount} of ${studentCards.length} ${studentCards.length === 1 ? 'student' : 'students'}.`;
        };

        searchInput?.addEventListener('input', applyFilters);
        schoolYearFilter?.addEventListener('change', applyFilters);
    }

    const firstInvalidField = document.querySelector('.student-create-form input[aria-invalid="true"]');
    if (firstInvalidField) firstInvalidField.focus();
})();
