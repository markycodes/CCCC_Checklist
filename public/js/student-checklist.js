(() => {
    const curriculum = window.studentCurriculum;
    const totalUnits = window.studentTotalUnits;
    const courseBase = document.body.dataset.courseBase;
    const courses = curriculum.flatMap((year, yearIndex) => year.semesters.flatMap((semester, semesterIndex) =>
        semester.courses.map((course) => ({ ...course, yearIndex, semesterIndex })),
    ));

    let courseGrades = new Map(Object.entries(window.completedCourseGrades || {}));
    let completed = new Set(
        [...courseGrades.entries()]
            .filter(([, grade]) => grade !== null && grade >= 1 && grade < 3)
            .map(([code]) => code),
    );
    let activeYear = 0;
    let activeSemester = 0;
    let searchQuery = '';
    let saveStatus = 'saved';
    const savingCodes = new Set();

    const yearTabs = document.querySelector('#year-tabs');
    const courseList = document.querySelector('#course-list');
    const searchInput = document.querySelector('#course-search');
    const semesterTabs = document.querySelector('#semester-tabs');
    const semesterButtons = [...document.querySelectorAll('.semester-tab')];
    const firstInvalidField = document.querySelector('.student-edit-form input[aria-invalid="true"]');

    if (firstInvalidField) firstInvalidField.focus();

    function currentSemester() {
        return curriculum[activeYear].semesters[activeSemester];
    }

    function setSaveStatus(status) {
        saveStatus = status;
        const label = document.querySelector('#save-label');
        const icon = document.querySelector('#save-icon');
        const messages = {
            saved: 'Saved to this student record',
            saving: 'Saving course progress…',
            error: 'Could not save — try again',
        };

        label.textContent = messages[status];
        icon.textContent = status === 'saved' ? '✓' : '!';
        icon.classList.toggle('is-warning', status !== 'saved');
    }

    function renderYearTabs() {
        yearTabs.replaceChildren(...curriculum.map((year, index) => {
            const yearCourses = courses.filter((course) => course.yearIndex === index);
            const done = yearCourses.filter((course) => completed.has(course.code)).length;
            const button = document.createElement('button');
            button.type = 'button';
            button.className = `year-tab${index === activeYear ? ' is-active' : ''}`;
            button.id = `year-tab-${index}`;
            button.setAttribute('role', 'tab');
            button.setAttribute('aria-selected', String(index === activeYear));
            button.setAttribute('aria-controls', 'course-list');
            button.tabIndex = index === activeYear ? 0 : -1;

            const number = document.createElement('span');
            number.className = 'year-number';
            number.textContent = `0${index + 1}`;

            const copy = document.createElement('span');
            copy.className = 'year-tab-copy';
            const name = document.createElement('strong');
            name.textContent = year.name;
            const count = document.createElement('span');
            count.textContent = `${done} / ${yearCourses.length} courses`;
            copy.append(name, count);
            button.append(number, copy);

            if (done === yearCourses.length) {
                const check = document.createElement('span');
                check.className = 'year-tab-check';
                check.setAttribute('aria-label', 'Year complete');
                check.textContent = '✓';
                button.append(check);
            }

            button.addEventListener('click', () => {
                activeYear = index;
                activeSemester = 0;
                render();
            });

            return button;
        }));
    }

    function renderCourses() {
        const semester = currentSemester();
        const filteredCourses = semester.courses.filter((course) =>
            `${course.code} ${course.title}`.toLowerCase().includes(searchQuery),
        );

        courseList.replaceChildren(...filteredCourses.map((course) => {
            const row = document.createElement('article');
            const isComplete = completed.has(course.code);
            row.className = `course-row${isComplete ? ' is-complete' : ''}`;
            row.dataset.courseId = course.code;

            const main = document.createElement('div');
            main.className = 'course-main';

            const gradeInput = document.createElement('input');
            gradeInput.className = 'course-grade-input';
            gradeInput.type = 'number';
            gradeInput.inputMode = 'decimal';
            gradeInput.min = '0';
            gradeInput.max = '5';
            gradeInput.step = '0.01';
            gradeInput.placeholder = '0.00';
            gradeInput.disabled = !window.canEnterGrades || savingCodes.has(course.code);
            gradeInput.setAttribute('aria-label', `Grade for ${course.code} ${course.title}`);

            const savedGrade = courseGrades.get(course.code);
            if (savedGrade !== undefined && savedGrade !== null) {
                gradeInput.value = Number(savedGrade).toFixed(2);
                gradeInput.classList.toggle('is-failing-grade', Number(savedGrade) >= 3);
            }

            let gradeTimer;
            gradeInput.addEventListener('input', () => {
                clearTimeout(gradeTimer);
                gradeTimer = setTimeout(() => saveCourseGrade(course, gradeInput), 500);
            });

            const copy = document.createElement('span');
            copy.className = 'course-copy';
            const title = document.createElement('span');
            title.className = 'course-title';
            title.textContent = course.title;
            const code = document.createElement('span');
            code.className = 'course-code';
            code.textContent = course.code;
            copy.append(title, code);
            main.append(gradeInput, copy);

            const credit = document.createElement('span');
            credit.className = 'course-credit';
            credit.textContent = `${course.units} ${course.units === 1 ? 'unit' : 'units'}`;

            const status = document.createElement('span');
            status.className = 'course-status';
            const rowGrade = courseGrades.get(course.code);
            status.textContent = rowGrade === undefined || rowGrade === null
                ? 'Grade needed'
                : (rowGrade >= 1 && rowGrade < 3 ? 'Completed' : 'Not passed');
            gradeInput.classList.toggle('is-failing-grade', rowGrade !== undefined && rowGrade !== null && Number(rowGrade) >= 3);
            row.append(main, credit, status);
            return row;
        }));

        const semesterCourses = semester.courses;
        const doneCount = semesterCourses.filter((course) => completed.has(course.code)).length;
        const semesterUnits = semesterCourses.reduce((sum, course) => sum + course.units, 0);
        const yearNumber = ['ONE', 'TWO', 'THREE', 'FOUR'][activeYear];
        const yearFocus = curriculum[activeYear].focus;
        document.querySelector('#semester-kicker').textContent = `YEAR ${yearNumber}${yearFocus ? ` · ${yearFocus.toUpperCase()}` : ''}`;
        document.querySelector('#semester-title').textContent = semester.name;
        document.querySelector('#semester-subtitle').textContent = `${semesterCourses.length} courses · ${semesterUnits} units`;
        document.querySelector('#semester-progress-text').textContent = `${doneCount} / ${semesterCourses.length}`;
        document.querySelector('#semester-progress-bar').style.width = `${semesterCourses.length ? (doneCount / semesterCourses.length) * 100 : 0}%`;
        document.querySelector('#empty-state').hidden = filteredCourses.length > 0;
        courseList.hidden = filteredCourses.length === 0;
        document.querySelectorAll('.course-list-head').forEach((head) => { head.hidden = filteredCourses.length === 0; });
        semesterButtons.forEach((button, index) => {
            const selected = index === activeSemester;
            button.classList.toggle('is-active', selected);
            button.setAttribute('aria-selected', String(selected));
            button.tabIndex = selected ? 0 : -1;
        });
    }

    function renderOverview() {
        const completeCourses = courses.filter((course) => completed.has(course.code));
        const count = completeCourses.length;
        const percent = Math.round((count / courses.length) * 100);
        const earned = completeCourses.reduce((sum, course) => sum + course.units, 0);
        const nextCourse = courses.find((course) => !completed.has(course.code));

        document.querySelector('#overall-percent').innerHTML = `${percent}<span>%</span>`;
        document.querySelector('#overall-detail').textContent = `${count} of ${courses.length} courses complete`;
        document.querySelector('#completed-count').textContent = count;
        document.querySelector('#course-count-detail').textContent = `across ${curriculum.length * 2} semesters`;
        document.querySelector('#earned-units').innerHTML = `${earned}<span class="unit-suffix"> / ${totalUnits}</span>`;
        document.querySelector('#ring-percent').textContent = `${percent}%`;
        document.querySelector('#progress-ring').style.setProperty('--progress', `${percent}%`);
        document.querySelector('#progress-ring').setAttribute('aria-label', `${percent} percent of courses completed`);
        document.querySelector('#next-course-title').textContent = nextCourse ? nextCourse.title : 'All courses completed';
        document.querySelector('#next-course-detail').textContent = nextCourse ? `${nextCourse.code} · ${nextCourse.units} ${nextCourse.units === 1 ? 'unit' : 'units'}` : 'All courses in this curriculum are marked complete.';
        setSaveStatus(saveStatus);
    }

    function render() {
        renderYearTabs();
        renderCourses();
        renderOverview();
    }

    async function saveCourseGrade(course, gradeInput) {
        const rawGrade = gradeInput.value.trim();
        const grade = Number(rawGrade);

        if (!rawGrade || grade === 0) {
            await clearCourseGrade(course);
            return;
        }

        if (!Number.isFinite(grade) || grade < 0 || grade > 5) {
            setSaveStatus('error');
            document.querySelector('#announcement').textContent = 'Enter a grade from 0.00 to 5.00.';
            gradeInput.setAttribute('aria-invalid', 'true');
            return;
        }

        gradeInput.removeAttribute('aria-invalid');
        savingCodes.add(course.code);
        setSaveStatus('saving');
        courseGrades.set(course.code, grade);
        if (grade >= 1 && grade < 3) completed.add(course.code);
        else completed.delete(course.code);
        render();

        try {
            const response = await fetch(`${courseBase}/${encodeURIComponent(course.code)}/completion`, {
                method: 'PUT',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ grade }),
            });

            if (!response.ok) {
                throw new Error(`The server returned ${response.status}.`);
            }

            setSaveStatus('saved');
            const result = await response.json();
            document.querySelector('#announcement').textContent = `${course.code} grade ${Number(result.grade).toFixed(2)} saved. ${result.completed ? 'Course completed.' : 'Course not passed.'}`;
        } catch (error) {
            courseGrades.delete(course.code);
            completed.delete(course.code);
            setSaveStatus('error');
            console.error(`Could not save progress for ${course.code}.`, error);
            document.querySelector('#announcement').textContent = `Could not save ${course.code}. Please try again.`;
        } finally {
            savingCodes.delete(course.code);
            render();
            courseList.querySelector(`[data-course-id="${CSS.escape(course.code)}"] .course-grade-input`)?.focus();
        }
    }

    async function clearCourseGrade(course) {
        savingCodes.add(course.code);
        setSaveStatus('saving');
        courseGrades.delete(course.code);
        completed.delete(course.code);
        render();

        try {
            const response = await fetch(`${courseBase}/${encodeURIComponent(course.code)}/completion`, {
                method: 'DELETE',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            });
            if (!response.ok) throw new Error(`The server returned ${response.status}.`);
            setSaveStatus('saved');
            document.querySelector('#announcement').textContent = `${course.code} grade cleared.`;
        } catch (error) {
            setSaveStatus('error');
            console.error(`Could not clear progress for ${course.code}.`, error);
            document.querySelector('#announcement').textContent = `Could not clear ${course.code}. Please try again.`;
        } finally {
            savingCodes.delete(course.code);
            render();
        }
    }

    semesterButtons.forEach((button) => {
        button.addEventListener('click', () => {
            activeSemester = Number(button.dataset.semester);
            render();
        });
    });

    yearTabs.addEventListener('keydown', (event) => {
        const currentTab = event.target.closest('.year-tab');
        if (!currentTab) return;

        let nextYear = activeYear;
        if (event.key === 'ArrowRight' || event.key === 'ArrowDown') nextYear = (activeYear + 1) % curriculum.length;
        else if (event.key === 'ArrowLeft' || event.key === 'ArrowUp') nextYear = (activeYear - 1 + curriculum.length) % curriculum.length;
        else if (event.key === 'Home') nextYear = 0;
        else if (event.key === 'End') nextYear = curriculum.length - 1;
        else return;

        event.preventDefault();
        activeYear = nextYear;
        activeSemester = 0;
        render();
        yearTabs.querySelector(`#year-tab-${activeYear}`).focus();
    });

    semesterTabs.addEventListener('keydown', (event) => {
        let nextSemester = activeSemester;
        if (event.key === 'ArrowRight' || event.key === 'ArrowDown') nextSemester = (activeSemester + 1) % semesterButtons.length;
        else if (event.key === 'ArrowLeft' || event.key === 'ArrowUp') nextSemester = (activeSemester - 1 + semesterButtons.length) % semesterButtons.length;
        else if (event.key === 'Home') nextSemester = 0;
        else if (event.key === 'End') nextSemester = semesterButtons.length - 1;
        else return;

        event.preventDefault();
        activeSemester = nextSemester;
        render();
        semesterButtons[nextSemester].focus();
    });

    searchInput.addEventListener('input', () => {
        searchQuery = searchInput.value.trim().toLowerCase();
        renderCourses();
    });

    document.addEventListener('keydown', (event) => {
        const activeElement = document.activeElement;
        const isInteractive = ['INPUT', 'TEXTAREA', 'SELECT', 'BUTTON', 'A'].includes(activeElement.tagName)
            || activeElement.isContentEditable;

        if (event.key === '/' && !isInteractive) {
            event.preventDefault();
            searchInput.focus();
        }
        if (event.key === 'Escape' && document.activeElement === searchInput) {
            searchInput.value = '';
            searchQuery = '';
            renderCourses();
            searchInput.blur();
        }
    });

    const studentSwitcherSearch = document.querySelector('#student-switcher-search');
    if (studentSwitcherSearch) {
        const studentSwitcherItems = [...document.querySelectorAll('.student-switcher-item')];
        const studentSwitcherEmpty = document.querySelector('#student-switcher-empty');

        studentSwitcherSearch.addEventListener('input', () => {
            const query = studentSwitcherSearch.value.trim().toLocaleLowerCase();
            let visibleCount = 0;

            studentSwitcherItems.forEach((item) => {
                const searchableText = `${item.dataset.studentName} ${item.dataset.studentId}`.toLocaleLowerCase();
                const matches = searchableText.includes(query);
                item.hidden = !matches;
                if (matches) visibleCount += 1;
            });

            studentSwitcherEmpty.hidden = visibleCount > 0;
        });
    }

    render();
})();
