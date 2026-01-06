function calculateNights(arrival, departure) {
  if (!arrival || !departure) return 0;

  const start = new Date(arrival);
  const end = new Date(departure);

  const diffTime = end - start;
  const diffDays = diffTime / (1000 * 60 * 60 * 24);

  return diffDays > 0 ? diffDays : 0;
}

function calculateTotal() {
  let roomPrice = 0;
  let roomTotal = 0;
  let featuresTotal = 0;

  const arrival = document.getElementById("arrival").value;
  const departure = document.getElementById("departure").value;

  const nights = calculateNights(arrival, departure);

  // rooms
  const selectedRoom = document.querySelector('input[name="room"]:checked');
  if (selectedRoom && nights > 0) {
    roomPrice = Number(selectedRoom.dataset.price);
    roomTotal = roomPrice * nights;
  }

  // features
  document
    .querySelectorAll('input[name="features[]"]:checked')
    .forEach((feature) => {
      featuresTotal += Number(feature.dataset.price);
    });

  const total = roomTotal + featuresTotal;

  // update values
  document.getElementById("room-price").textContent = roomPrice;
  document.getElementById("nights").textContent = nights;
  document.getElementById("features-price").textContent = featuresTotal;
  document.getElementById("total-price").textContent = total;
}

// listeners
document
  .querySelectorAll(
    '#arrival, #departure, input[name="room"], input[name="features[]"]'
  )
  .forEach((input) => {
    input.addEventListener("change", calculateTotal);
  });
