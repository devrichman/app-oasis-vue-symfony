import Paginate from "vuejs-paginate";

export default {
  name: "List",
  props: {
    name: {
      type: String,
      required: true
    },
    header: {
      type: Array,
      required: true
    },
    items: {
      type: Array,
      required: true
    },
    noItemsMessage: {
      type: String,
      required: true
    },
    total: {
      type: Number,
      required: true
    },
    defaultSort: {
      type: String,
      required: false
    },
    defaultSortDirection: {
      type: String,
      default: "desc"
    },
    loading: {
      type: Boolean,
      default: false
    },
    perPage: {
      type: Number,
      default: 10
    },
    actionRow: {
      type: Boolean,
      default: false
    },
    actionIcon: {
      type: String,
      default: ""
    },
    pagination: {
      type: Boolean,
      default: true
    },
    showAvatar: {
      type: Boolean,
      default: false
    }
  },
  data: () => ({
    sortColumn: "",
    sortDirection: "desc",
    totalPages: 0,
    actions: {
      edit: {
        icon: "edit",
        labelToolTip: "Editer"
      },
      disable: {
        icon: "lock-on",
        labelToolTip: "Désactiver"
      },
      enable: {
        icon: "lock-off",
        labelToolTip: "Activer"
      },
      delete: {
        icon: "archive",
        labelToolTip: "Archiver"
      },
      see: {
        icon: "see",
        labelToolTip: "Voir"
      }
    },
    showActions: null,
    page: 1,
  }),
  components: {
    Paginate
  },
  computed: {
    widths() {
      let totalWidth = 100,
        count = this.header.length,
        widths = {};
      // Take defined widths and divide the remaining width equally amongst the other columns
      this.header.forEach(column => {
        if (column.width) {
          totalWidth -= column.width ? column.width : 0;
          count--;
        }
      });
      this.header.forEach(column => {
        widths[column.key] =
          (column.width ? column.width : totalWidth / count) + "%";
      });
      return widths;
    }
  },
  methods: {
    headerClick(index) {
      let column = this.header[index];
      if (!column.sortable) {
        return;
      }

      if (this.sortColumn === column.key) {
        this.sortDirection = this.sortDirection === "asc" ? "desc" : "asc";
      } else {
        this.sortColumn = column.key;
        this.sortDirection = "desc";
      }
      this.$emit("sort", {
        column: this.sortColumn,
        direction: this.sortDirection
      });
    },
    resetPage() {
      this.paginate(1);
    },
    paginate(page) {
      this.page = page;
      this.$emit('paginate', page);
    },
    statusStyle(status) {
      if (!status) {
        return "-undefined";
      }
      var customClass,
        st = status.toString().toLowerCase();
      switch (st) {
        case "oui":
          customClass = "oui";
          break;

        case "non":
          customClass = "non";
          break;

        case "à venir":
          customClass = "style-3";
          break;

        case "en cours":
          customClass = "style-4";
          break;

        case "terminée":
          customClass = "style-5";
          break;

        case "archivée":
          customClass = "style-6";
          break;

        default:
          break;
      }
      return customClass;
    },
    stripTags(str) {
      if (!str) {
        return [];
      }
      return str.split(", ");
    }
  },
  watch: {
    total() {
      this.totalPages = Math.ceil(this.total / this.perPage);
    }
  },
  mounted() {
    if (this.defaultSort) {
      this.sortColumn = this.defaultSort;
    }
    if (this.defaultSortDirection) {
      this.sortDirection = this.defaultSortDirection;
    }
    this.totalPages = Math.ceil(this.total / this.perPage);
  }
};
