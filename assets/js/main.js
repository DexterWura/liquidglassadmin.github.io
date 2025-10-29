// Liquid Glass Admin Dashboard - JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('open');
            document.body.classList.toggle('sidebar-open');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 767) {
                if (sidebar.classList.contains('open')) {
                    if (!sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
                        sidebar.classList.remove('open');
                        document.body.classList.remove('sidebar-open');
                    }
                }
            }
        });

        // Close sidebar on window resize if it becomes desktop size
        window.addEventListener('resize', function() {
            if (window.innerWidth > 767) {
                sidebar.classList.remove('open');
                document.body.classList.remove('sidebar-open');
            }
        });

        // Close sidebar when clicking nav items on mobile
        const navItems = document.querySelectorAll('.nav-item');
        navItems.forEach(item => {
            item.addEventListener('click', function() {
                if (window.innerWidth <= 767) {
                    sidebar.classList.remove('open');
                    document.body.classList.remove('sidebar-open');
                }
            });
        });
    }

    // Chart controls
    const chartButtons = document.querySelectorAll('.chart-btn');
    chartButtons.forEach(button => {
        button.addEventListener('click', function() {
            chartButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            // Here you would update the chart data based on the selected period
        });
    });

    // Initialize Charts
    initRevenueChart();
    initActivityChart();
    initTrafficChart();
    initSalesPieChart();
    initGrowthChart();
    initCategoryChart();
    initComparisonChart();
});

// Revenue Chart
function initRevenueChart() {
    const ctx = document.getElementById('revenueChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Revenue',
                data: [3200, 4500, 3800, 5200, 4900, 6100, 5800],
                borderColor: '#007aff',
                backgroundColor: 'rgba(0, 122, 255, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#007aff',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: '#007aff',
                pointHoverBorderColor: '#ffffff',
                pointHoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.85)',
                    padding: 12,
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return '$' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: 'rgba(0, 0, 0, 0.5)',
                        font: {
                            size: 12
                        }
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.06)',
                        drawBorder: false
                    },
                    ticks: {
                        color: 'rgba(0, 0, 0, 0.5)',
                        font: {
                            size: 12
                        },
                        callback: function(value) {
                            return '$' + value + 'k';
                        }
                    }
                }
            }
        }
    });
}

// Activity Chart
function initActivityChart() {
    const ctx = document.getElementById('activityChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Active Users',
                data: [420, 580, 510, 670, 720, 890, 760],
                backgroundColor: '#5856d6',
                borderColor: '#5856d6',
                borderWidth: 0,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.85)',
                    padding: 12,
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + ' users';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: 'rgba(0, 0, 0, 0.5)',
                        font: {
                            size: 12
                        }
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.06)',
                        drawBorder: false
                    },
                    ticks: {
                        color: 'rgba(0, 0, 0, 0.5)',
                        font: {
                            size: 12
                        },
                        stepSize: 200
                    }
                }
            }
        }
    });
}

// Traffic Chart (Doughnut)
function initTrafficChart() {
    const ctx = document.getElementById('trafficChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Organic Search', 'Direct', 'Social Media', 'Referral', 'Email'],
            datasets: [{
                data: [35, 28, 20, 12, 5],
                backgroundColor: [
                    '#007aff',
                    '#5856d6',
                    '#af52de',
                    '#ff2d55',
                    '#ff9500'
                ],
                borderColor: [
                    '#ffffff',
                    '#ffffff',
                    '#ffffff',
                    '#ffffff',
                    '#ffffff'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: 'rgba(0, 0, 0, 0.7)',
                        padding: 15,
                        font: {
                            size: 12
                        },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.85)',
                    padding: 12,
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            return label + ': ' + value + '%';
                        }
                    }
                }
            },
            cutout: '70%'
        }
    });
}

// Animate progress bars on scroll
const observerOptions = {
    threshold: 0.5,
    rootMargin: '0px'
};

const progressObserver = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const progressFill = entry.target.querySelector('.progress-fill');
            if (progressFill) {
                const width = progressFill.style.width;
                progressFill.style.width = '0%';
                setTimeout(() => {
                    progressFill.style.width = width;
                }, 100);
            }
        }
    });
}, observerOptions);

document.querySelectorAll('.product-item').forEach(item => {
    progressObserver.observe(item);
});

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Update stats animation
function animateValue(element, start, end, duration) {
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        const current = Math.floor(progress * (end - start) + start);
        
        if (element.textContent.includes('$')) {
            element.textContent = '$' + current.toLocaleString();
        } else if (element.textContent.includes('%')) {
            element.textContent = (progress * (end - start) + start).toFixed(2) + '%';
        } else {
            element.textContent = current.toLocaleString();
        }
        
        if (progress < 1) {
            window.requestAnimationFrame(step);
        }
    };
    window.requestAnimationFrame(step);
}

// Animate stat values on load
const statValues = document.querySelectorAll('.stat-value');
statValues.forEach(stat => {
    const finalValue = stat.textContent;
    let numericValue = parseFloat(finalValue.replace(/[^0-9.]/g, ''));
    
    if (!isNaN(numericValue)) {
        stat.textContent = '0';
        setTimeout(() => {
            animateValue(stat, 0, numericValue, 1500);
        }, 500);
    }
});

// Sales Pie Chart
function initSalesPieChart() {
    const ctx = document.getElementById('salesPieChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Electronics', 'Clothing', 'Books', 'Accessories', 'Other'],
            datasets: [{
                data: [35, 28, 20, 12, 5],
                backgroundColor: [
                    '#007aff',
                    '#5856d6',
                    '#af52de',
                    '#ff2d55',
                    '#ff9500'
                ],
                borderColor: '#ffffff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: 'rgba(0, 0, 0, 0.7)',
                        padding: 15,
                        font: { size: 12 },
                        usePointStyle: true
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.85)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + '%';
                        }
                    }
                }
            }
        }
    });
}

// Growth Chart (Area Chart)
function initGrowthChart() {
    const ctx = document.getElementById('growthChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Sales',
                data: [12000, 19000, 15000, 25000, 22000, 30000],
                borderColor: '#007aff',
                backgroundColor: 'rgba(0, 122, 255, 0.2)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }, {
                label: 'Revenue',
                data: [18000, 24000, 20000, 30000, 28000, 35000],
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.2)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: 'rgba(0, 0, 0, 0.7)',
                        padding: 15,
                        font: { size: 12 }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.85)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff'
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: 'rgba(0, 0, 0, 0.5)' }
                },
                y: {
                    grid: { color: 'rgba(0, 0, 0, 0.06)' },
                    ticks: { 
                        color: 'rgba(0, 0, 0, 0.5)',
                        callback: function(value) { return '$' + (value/1000) + 'k'; }
                    }
                }
            }
        }
    });
}

// Category Performance Chart (Radar)
function initCategoryChart() {
    const ctx = document.getElementById('categoryChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['Sales', 'Revenue', 'Users', 'Engagement', 'Retention', 'Satisfaction'],
            datasets: [{
                label: 'Electronics',
                data: [85, 90, 75, 80, 85, 88],
                borderColor: '#007aff',
                backgroundColor: 'rgba(0, 122, 255, 0.2)',
                borderWidth: 2
            }, {
                label: 'Clothing',
                data: [70, 75, 85, 90, 75, 82],
                borderColor: '#5856d6',
                backgroundColor: 'rgba(88, 86, 214, 0.2)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: 'rgba(0, 0, 0, 0.7)',
                        padding: 15,
                        font: { size: 12 }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.85)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff'
                }
            },
            scales: {
                r: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        color: 'rgba(0, 0, 0, 0.5)',
                        backdropColor: 'transparent'
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    },
                    pointLabels: {
                        color: 'rgba(0, 0, 0, 0.7)',
                        font: { size: 11 }
                    }
                }
            }
        }
    });
}

// Monthly Comparison Chart (Bar)
function initComparisonChart() {
    const ctx = document.getElementById('comparisonChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Last Year',
                data: [15000, 18000, 20000, 22000, 24000, 26000],
                backgroundColor: 'rgba(0, 0, 0, 0.1)',
                borderColor: 'rgba(0, 0, 0, 0.3)',
                borderWidth: 1,
                borderRadius: 8
            }, {
                label: 'This Year',
                data: [18000, 24000, 20000, 30000, 28000, 35000],
                backgroundColor: '#007aff',
                borderColor: '#007aff',
                borderWidth: 0,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: 'rgba(0, 0, 0, 0.7)',
                        padding: 15,
                        font: { size: 12 }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.85)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff'
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: 'rgba(0, 0, 0, 0.5)' }
                },
                y: {
                    grid: { color: 'rgba(0, 0, 0, 0.06)' },
                    ticks: { 
                        color: 'rgba(0, 0, 0, 0.5)',
                        callback: function(value) { return '$' + (value/1000) + 'k'; }
                    }
                }
            }
        }
    });
}

