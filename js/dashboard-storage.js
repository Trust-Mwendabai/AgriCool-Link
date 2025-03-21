document.addEventListener('DOMContentLoaded', function() {
    // Toggle Sidebar
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    }

    // Storage Units Temperature Chart
    const storageCtx = document.getElementById('storageChart');
    if (storageCtx) {
        new Chart(storageCtx, {
            type: 'line',
            data: {
                labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00'],
                datasets: [
                    {
                        label: 'Unit A',
                        data: [4, 4.2, 4.1, 4.3, 4, 4.1],
                        borderColor: '#198754',
                        tension: 0.4
                    },
                    {
                        label: 'Unit B',
                        data: [4.5, 5, 5.5, 6, 5.8, 6],
                        borderColor: '#ffc107',
                        tension: 0.4
                    },
                    {
                        label: 'Unit C',
                        data: [3.8, 3.5, 3.2, 3, 3.2, 3],
                        borderColor: '#0dcaf0',
                        tension: 0.4
                    },
                    {
                        label: 'Unit D',
                        data: [7, 7.5, 8, 8.2, 8.5, 8],
                        borderColor: '#dc3545',
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: 'Temperature Monitoring (°C)'
                    }
                },
                scales: {
                    y: {
                        min: 0,
                        max: 10,
                        ticks: {
                            stepSize: 2
                        }
                    }
                }
            }
        });
    }

    // Power Consumption Chart
    const powerCtx = document.getElementById('powerChart');
    if (powerCtx) {
        new Chart(powerCtx, {
            type: 'doughnut',
            data: {
                labels: ['Main Power', 'Generator', 'Solar'],
                datasets: [{
                    data: [65, 20, 15],
                    backgroundColor: [
                        '#198754',
                        '#ffc107',
                        '#0dcaf0'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: 'Power Source Distribution'
                    }
                }
            }
        });
    }

    // Storage Unit Monitoring
    function updateStorageUnits() {
        const units = document.querySelectorAll('.storage-unit-card');
        units.forEach(unit => {
            // Simulate temperature changes
            const tempSpan = unit.querySelector('.text-success, .text-warning, .text-danger');
            if (tempSpan) {
                const currentTemp = parseFloat(tempSpan.textContent);
                const fluctuation = (Math.random() - 0.5) * 0.5;
                const newTemp = (currentTemp + fluctuation).toFixed(1);
                tempSpan.textContent = `${newTemp}°C`;

                // Update temperature status
                if (newTemp > 6) {
                    tempSpan.className = 'text-danger';
                } else if (newTemp > 4) {
                    tempSpan.className = 'text-warning';
                } else {
                    tempSpan.className = 'text-success';
                }
            }

            // Simulate humidity changes
            const humidityBar = unit.querySelector('.progress-bar.bg-info');
            if (humidityBar) {
                const currentHumidity = parseInt(humidityBar.style.width);
                const fluctuation = Math.floor((Math.random() - 0.5) * 5);
                const newHumidity = Math.min(100, Math.max(0, currentHumidity + fluctuation));
                humidityBar.style.width = `${newHumidity}%`;
                const humiditySpan = unit.querySelector('.humidity-value');
                if (humiditySpan) {
                    humiditySpan.textContent = `${newHumidity}%`;
                }
            }
        });
    }

    // Update storage units every 5 seconds
    setInterval(updateStorageUnits, 5000);

    // Storage Unit Details Button
    const detailButtons = document.querySelectorAll('.storage-unit-card .btn-outline-primary');
    detailButtons.forEach(button => {
        button.addEventListener('click', function() {
            const unitName = this.closest('.storage-unit-card').querySelector('.card-title').textContent;
            showStorageUnitDetails(unitName);
        });
    });

    function showStorageUnitDetails(unitName) {
        showNotification(`Viewing details for ${unitName}`);
        // Here you would typically show a modal with detailed information
    }

    // Power Source Monitoring
    function updatePowerStatus() {
        const mainPower = document.querySelector('.backup-status:first-child .badge');
        const generator = document.querySelector('.backup-status:nth-child(2) .badge');
        const solar = document.querySelector('.backup-status:last-child .badge');

        // Simulate power status changes
        if (Math.random() < 0.1) { // 10% chance of power interruption
            mainPower.className = 'badge bg-danger';
            mainPower.textContent = 'Interrupted';
            generator.className = 'badge bg-warning';
            generator.textContent = 'Active';
        } else {
            mainPower.className = 'badge bg-success';
            mainPower.textContent = 'Active';
            generator.className = 'badge bg-success';
            generator.textContent = 'Standby';
        }

        // Update battery levels
        const batteryLevel = document.querySelector('.backup-status:last-child .progress-bar');
        if (batteryLevel) {
            const currentLevel = parseInt(batteryLevel.style.width);
            const change = Math.floor((Math.random() - 0.3) * 5); // Bias towards charging
            const newLevel = Math.min(100, Math.max(0, currentLevel + change));
            batteryLevel.style.width = `${newLevel}%`;
            const levelText = batteryLevel.closest('.backup-status').querySelector('small');
            if (levelText) {
                levelText.textContent = `Battery Level: ${newLevel}%`;
            }
        }
    }

    // Update power status every 10 seconds
    setInterval(updatePowerStatus, 10000);

    // Add Storage Unit Button
    const addUnitBtn = document.querySelector('.btn-add-unit');
    if (addUnitBtn) {
        addUnitBtn.addEventListener('click', function() {
            showNotification('Opening add storage unit form...');
            // Here you would typically show a modal with the add unit form
        });
    }

    // Temperature Filter Buttons
    const tempFilterButtons = document.querySelectorAll('.btn-group .btn-outline-primary');
    tempFilterButtons.forEach(button => {
        button.addEventListener('click', function() {
            tempFilterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            updateTemperatureChart(this.textContent.toLowerCase());
        });
    });

    function updateTemperatureChart(metric) {
        showNotification(`Showing ${metric} data`);
        // Here you would typically update the chart with new data
    }

    // Activity Timeline
    function addNewActivity(activity) {
        const timeline = document.querySelector('.activity-timeline');
        if (timeline) {
            const activityItem = document.createElement('div');
            activityItem.className = 'activity-item d-flex';
            activityItem.innerHTML = `
                <div class="activity-content">
                    <small class="text-muted">Just now</small>
                    <h6 class="mb-1">${activity.title}</h6>
                    <p class="mb-0">${activity.description}</p>
                </div>
            `;
            timeline.insertBefore(activityItem, timeline.firstChild);
            if (timeline.children.length > 4) {
                timeline.lastChild.remove();
            }
        }
    }

    // Simulate new activities
    setInterval(() => {
        if (Math.random() < 0.3) { // 30% chance of new activity
            const activities = [
                {
                    title: 'Temperature Alert',
                    description: 'Unit B temperature rising above optimal range.'
                },
                {
                    title: 'New Storage Request',
                    description: 'Farmer John requested storage space for vegetables.'
                },
                {
                    title: 'Power System Update',
                    description: 'Backup generator maintenance completed.'
                },
                {
                    title: 'Humidity Alert',
                    description: 'Unit C humidity levels need adjustment.'
                }
            ];
            const activity = activities[Math.floor(Math.random() * activities.length)];
            addNewActivity(activity);
        }
    }, 15000);

    // Notification System
    function showNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'notification';
        notification.innerHTML = `
            <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.firstChild.classList.remove('show');
            setTimeout(() => notification.remove(), 150);
        }, 3000);
    }

    // Initialize tooltips and popovers
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
});
