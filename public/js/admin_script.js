document.addEventListener('DOMContentLoaded', function () {
    const setupValidation = (elementId, message) => {
        const element = document.getElementById(elementId);
        if (element) {
            element.addEventListener('invalid', (e) => {
                e.target.setCustomValidity(message);
            });
            const resetValidity = (e) => e.target.setCustomValidity('');
            element.addEventListener('input', resetValidity);
            element.addEventListener('change', resetValidity); 
        }
    };

    setupValidation('name', 'Vui lòng nhập tên cho sản phẩm.');
    setupValidation('categoryId', 'Vui lòng chọn một danh mục.');
    setupValidation('brandId', 'Vui lòng chọn một thương hiệu.');
    setupValidation('price', 'Vui lòng nhập giá sản phẩm.');
    setupValidation('stock_quantity', 'Vui lòng nhập số lượng tồn kho.');
    setupValidation('image', 'Vui lòng chọn ảnh cho sản phẩm.');


    const specsContainer = document.getElementById('specs-container');
    if (specsContainer) {
        const addSpecBtn = document.getElementById('add-spec-btn');
        const descriptionJsonInput = document.getElementById('description-json');
        const form = document.querySelector('form');

        const addSpecRow = (key = '', value = '') => {
            const row = document.createElement('div');
            row.className = 'row g-2 mb-2 spec-row';
            row.innerHTML = `
                <div class="col-md-4"><input type="text" class="form-control form-control-sm spec-key" value="${key}" placeholder="Tên thông số"></div>
                <div class="col-md-7"><textarea class="form-control form-control-sm spec-value" rows="1" placeholder="Giá trị">${value}</textarea></div>
                <div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger w-100 remove-spec-btn">&times;</button></div>
            `;
            specsContainer.appendChild(row);
            const textarea = row.querySelector('.spec-value');
            if (textarea) {
                textarea.style.height = 'auto';
                textarea.style.height = (textarea.scrollHeight || 0) + 'px'; 
            }
        };

        if (addSpecBtn) {
            addSpecBtn.addEventListener('click', () => addSpecRow());
        }

        specsContainer.addEventListener('click', (e) => {
            if (e.target.closest('.remove-spec-btn')) {
                e.target.closest('.spec-row').remove();
            }
        });

        if (form) {
            form.addEventListener('submit', () => {
                const specs = [];
                document.querySelectorAll('.spec-row').forEach(row => {
                    const key = row.querySelector('.spec-key').value.trim();
                    const value = row.querySelector('.spec-value').value.trim();
                    if (key || value) specs.push({ key, value });
                });
                descriptionJsonInput.value = JSON.stringify(specs);
            });
        }

        if (descriptionJsonInput && descriptionJsonInput.value.trim()) {
            try {
                const decodedJson = (new DOMParser().parseFromString(descriptionJsonInput.value, "text/html")).documentElement.textContent;
                const existingSpecs = JSON.parse(decodedJson);
                if (Array.isArray(existingSpecs)) {
                    existingSpecs.forEach(spec => addSpecRow(spec.key, spec.value));
                }
            } catch (e) {
                console.error("Lỗi đọc JSON từ mô tả:", e);
            }
        }

        const parseSpecsBtn = document.getElementById('parse-specs-btn');
        const pasteSpecsTextarea = document.getElementById('paste-specs');
        if (parseSpecsBtn && pasteSpecsTextarea) {
            parseSpecsBtn.addEventListener('click', () => {
                const text = pasteSpecsTextarea.value.trim();
                if (!text) return alert('Vui lòng dán thông số vào ô.');

                specsContainer.innerHTML = '';
                const lines = text.split(/\r?\n/);
                let currentKey = '', currentValue = '', specs = [];

                lines.forEach(line => {
                    const parts = line.split('\t');
                    if (parts.length > 1 && parts[0].trim()) {
                        if (currentKey) specs.push({ key: currentKey, value: currentValue.trim() });
                        currentKey = parts[0].trim();
                        currentValue = parts.slice(1).join('\t').trim();
                    } else if (currentKey) {
                        currentValue += '\n' + line.trim();
                    }
                });

                if (currentKey) specs.push({ key: currentKey, value: currentValue.trim() });
                specs.forEach(spec => addSpecRow(spec.key, spec.value));
                pasteSpecsTextarea.value = '';
            });
        }
    }

    const priceInputs = document.querySelectorAll('.price-input');
    priceInputs.forEach(input => {
        const formatNumber = (numStr) => {
            if (!numStr) return '';
            const rawNum = numStr.replace(/[.,]/g, '');
            return parseInt(rawNum, 10).toLocaleString('vi-VN');
        };

        input.value = formatNumber(input.value);

        input.addEventListener('input', () => {
            input.value = formatNumber(input.value);
        });
    });
});