// assets/js/script.js
// 通用功能脚本

// 改变商品数量
function changeQuantity(button, change) {
    const input = button.parentElement.querySelector('.quantity-input');
    let value = parseInt(input.value) + change;
    const max = parseInt(input.max);
    const min = parseInt(input.min);
    
    if (value > max) value = max;
    if (value < min) value = min;
    
    input.value = value;
}

// 改变购物车商品数量
function changeCartQuantity(button, change) {
    const input = button.parentElement.querySelector('.quantity-input');
    let value = parseInt(input.value) + change;
    const max = parseInt(input.max);
    const min = parseInt(input.min);
    
    if (value > max) value = max;
    if (value < min) value = min;
    
    input.value = value;
}

// 标签页切换
function openTab(tabName) {
    // 隐藏所有标签内容
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(tab => tab.classList.remove('active'));
    
    // 移除所有标签按钮的active类
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => button.classList.remove('active'));
    
    // 显示选中的标签内容
    document.getElementById(`${tabName}-tab`).classList.add('active');
    
    // 激活选中的标签按钮
    event.currentTarget.classList.add('active');
}

// 图片预览功能
document.addEventListener('DOMContentLoaded', function() {
    // 如果有图片上传输入框，添加预览功能
    const imageInput = document.getElementById('image');
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // 移除现有的预览
                    const existingPreview = document.querySelector('.image-preview');
                    if (existingPreview) {
                        existingPreview.remove();
                    }
                    
                    // 创建新的预览
                    const preview = document.createElement('div');
                    preview.className = 'current-image';
                    preview.innerHTML = `
                        <p>预览图片:</p>
                        <img src="${e.target.result}" alt="预览" class="current-image-preview">
                    `;
                    
                    // 插入到文件输入框后面
                    imageInput.parentNode.appendChild(preview);
                }
                reader.readAsDataURL(file);
            }
        });
    }
    
    // 自动关闭消息通知
    const messages = document.querySelectorAll('.message');
    messages.forEach(message => {
        setTimeout(() => {
            message.style.opacity = '0';
            setTimeout(() => {
                message.style.display = 'none';
            }, 300);
        }, 5000);
    });
});
