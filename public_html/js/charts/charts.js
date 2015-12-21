var chart_basic = {
  chart: {
    renderTo: "",
    type: "column",
    zoomType: "x"
  },
  title: {
    text: "",
    style: {
      color: "#000000",
      fontSize: "9px",
      padding: "5px"
    }
  },
  subtitle: {
    text: ""
  },
  xAxis: {
    categories: [],
    title: {
      text: null
    },
    labels: {
      rotation: -45,
      y: 30
    },
    style: {
      color: "#6D869F",
      fontWeight: "bold"
    }
  },
  yAxis: {
    min: 0,
    title: {
      text: "",
      align: "high"
    },
    stackLabels: {
      enabled: true,
      style: {
        fontWeight: "normal"
      }
    }
  },
  colors: ["#2c4359"],
  tooltip: {
    formatter: function() {
      return this.y;
    },
    style: {
      color: "#000000",
      fontSize: "9pt",
      padding: "5px"
    }
  },
  plotOptions: {
    column: {
      cursor: "pointer",
      dataLabels: {
        align: "center",
        y: -2,
        enabled: true,
        color: "black",
        style: {
          fontWeight: "bold",
          padding: "5px",
          fontSize: "9pt"
        },
        formatter: function() {
          if (this.y) {
            return this.y;
          }
        }
      }
    }
  },
  legend: {
    enabled: true,
    layout: "horizontal",
    borderWidth: 1,
    backgroundColor: "#ffffff",
    shadow: false,
    reversed: false
  },
  credits: {
    enabled: false
  },
  series: []
}