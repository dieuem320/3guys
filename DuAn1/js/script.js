navbar = document.querySelector('.header .flex .navbar');

document.querySelector('#menu-btn').onclick = () =>{
   navbar.classList.toggle('active');
   profile.classList.remove('active');
}

profile = document.querySelector('.header .flex .profile');

document.querySelector('#user-btn').onclick = () =>{
   profile.classList.toggle('active');
   navbar.classList.remove('active');
}

window.onscroll = () =>{
   navbar.classList.remove('active');
   profile.classList.remove('active');
}

function loader(){
   document.querySelector('.loader').style.display = 'none';
}

function fadeOut(){
   setInterval(loader, 1000);
}

window.onload = fadeOut;

// document.querySelectorAll('input[type="number"]').forEach(numberInput => {
//    numberInput.oninput = () =>{
//       if(numberInput.value.length > numberInput.maxLength) numberInput.value = numberInput.value.slice(0, numberInput.maxLength);
//    };
// });
// function applyDiscount() {
//    var discountCode = document.getElementById('discount').value;

//    // Gọi Ajax hoặc Fetch để gửi mã giảm giá và nhận kết quả từ máy chủ
//    // Đây là một ví dụ sử dụng Fetch API
//    fetch('check_discount.php', {
//        method: 'POST',
//        headers: {
//            'Content-Type': 'application/json',
//        },
//        body: JSON.stringify({ code: discountCode }),
//    })
//    .then(response => response.json())
//    .then(data => {
//        if (data.valid) {
//            alert('Mã giảm giá hợp lệ! Đang áp dụng giảm giá...');
//            // Thực hiện trừ tiền ngay lập tức (có thể gọi hàm trừ tiền ở đây)
//            applyDiscountImmediately();
//        } else {
//            alert('Mã giảm giá không hợp lệ hoặc không đủ điều kiện.');
//        }
//    })
//    .catch(error => {
//        console.error('Lỗi khi kiểm tra mã giảm giá:', error);
//    });
// }

// function applyDiscountImmediately() {
//    // Thực hiện các bước để trừ tiền ngay lập tức, ví dụ:
//    // 1. Cập nhật tổng giá trị đơn hàng trên giao diện người dùng
//    // 2. Gửi yêu cầu đặt hàng với giảm giá lên máy chủ
//    // 3. Hiển thị thông báo thành công

//    // Đây là một ví dụ giả định:
//    document.getElementById('total_price').innerHTML = 'Tổng giá trị đơn hàng sau giảm giá: ' + data.final_price + 'đ';
//    alert('Áp dụng giảm giá thành công!');
// }