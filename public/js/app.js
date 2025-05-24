// public/js/app.js

document.addEventListener('DOMContentLoaded', () => {
  console.log('Smart Bus Monitoring app.js loaded');

  const passengerCountElem = document.getElementById('passengerCount');
  const alertElem = document.getElementById('alertMessage');

  // Maximum capacity of the bus (can be set dynamically from server)
  const maxCapacity = 40;

  // To prevent repeated alert requests flooding the server
  let alertSent = false;

  // Fetch passenger count from backend API
  async function fetchPassengerCount() {
    try {
      const response = await fetch('/api/fetch_data.php');
      if (!response.ok) throw new Error('Network response was not ok');

      const data = await response.json();
      // Example data format expected: { passenger_count: 30 }

      const count = data.passenger_count;

      if (passengerCountElem) {
        passengerCountElem.textContent = count;
      }

      if (count > maxCapacity) {
        if (alertElem) {
          alertElem.textContent = '⚠️ Warning: Bus Overloaded!';
          alertElem.style.color = 'red';
          alertElem.style.fontWeight = 'bold';
        }

        // Send alert once when overload detected
        if (!alertSent) {
          sendOverloadAlert(count);
          alertSent = true;
        }
      } else {
        if (alertElem) {
          alertElem.textContent = '';
        }
        alertSent = false; // Reset alert flag when below capacity
      }
    } catch (error) {
      console.error('Error fetching passenger count:', error);
      if (alertElem) {
        alertElem.textContent = 'Error fetching data.';
        alertElem.style.color = 'orange';
      }
    }
  }

  // Send alert to backend (e.g., send SMS or notification)
  async function sendOverloadAlert(currentCount) {
    try {
      const response = await fetch('/api/send_alert.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ passenger_count: currentCount })
      });

      const result = await response.json();

      if (result.success) {
        console.log('Alert sent successfully');
      } else {
        console.warn('Failed to send alert:', result.message);
      }
    } catch (error) {
      console.error('Error sending alert:', error);
    }
  }

  // Initial fetch when page loads
  fetchPassengerCount();

  // Update passenger count every 10 seconds
  setInterval(fetchPassengerCount, 10000);
});
