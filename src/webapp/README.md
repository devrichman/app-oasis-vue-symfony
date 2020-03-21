# webapp

## Project setup
```
npm install
```

### Compiles and hot-reloads for development
```
npm run serve
```

### Compiles and minifies for production
```
npm run build
```

### Lints and fixes files
```
npm run lint
```

### Customize configuration
See [Configuration Reference](https://cli.vuejs.org/config/).

## List Component
The webapp has a general purpose List component which can show a table of data. It supports pagination and sorting.
For an example of usage with GraphQL, see usersList.vue. 



### Usage
```vue
<list :items="listItems"
      :total="itemsCount"
      :header="header"
      :loading="loading"
      :per-page="10"
      :default-sort="'sortField'"
      :default-sort-direction="'desc'"
      @sort="$event => updateSort($event.sortColumn, $event.sortDirection)"
      @paginate="currentPage => updatePage(currentPage)" />
```
* `items` is an array of Objects which contain the rows of data
* `total` is the total number of items
* `header` is an array of Objects containing the column definition
* `loading` (optional) whether to display a Loading indicator
* `perPage` (optional) the number of items to show per page (default 10)
* `defaultSort` (optional) the column to sort by
* `defaultSortDirection` (optional) the sort direction (default desc)

For status item, there are 3 classes :

* `.cell` : wrapper for all `<td></td>`'s
* `.cell-status` for the status `.cell` element, that add styles for font, padding, border-radius etc
* `.cell-oui/.cell-non` for the color and background-color depending on the value of the status. There is a method, `statusStyle()` that accepts the status value and runs a switch loop to return appropriate class. All the colors as per the design have been added in the css as classes.

### Header definition
Each Object in the `header` array defines a column which can have the following structure:
```js
let header = [
    {
        key: 'actions',
        label: 'Actions',
        sortable: true,
        // Class for the header's th column 
        classList: {'header-class': true}, // optional
        // If the column is supposed to have actions, then the following is required:
        actions: ['edit', 'view', 'delete'],
    },
    {
        key: 'name',
        label: 'Nom',
        sortable: false,
    }
];
```
### Data
Each row needs to have a value for each header.column, indexed by column.key,
unless the column has actions, then it is not required. 
```js
let items = [
    {id: 1, name: 'Name 1'},
    {
        id: 2, // Fields without a header column are ignored
        name: 'Name 2',
        // This class will only appear for this row's <tr> element
        _classList: {'test-row': true},
        // If you want to disable a certain button for this row, by default any action
        // not mentioned here is enabled
        _actions: {'edit': false},
    },
];
```
### Actions
Each action on a row will emit an event equivalent to action.key, and the callback will
be passed the entire row
```vue
<list @edit="item => editItem(item.id)" />
```

## Filters
Filters is another general purpose component 

### Usage
```vue
<filters :filters="filters"
         @filter="updateFilters($event)" />
```
Where `filters` are defined as:
```js
let filters = [
    {key: 'search', type: 'text', label: 'Nom, Prénom ou Email'},
    {
        key: 'role',
        type: 'select',
        attributes: {
            options: [
                {value: 1, label: 'Role 1'},
                {value: 2, label: 'Role 2'},
            ],
        },
    },
];
```
Currently supported types are `text` and `select`. The callback for `@filter` event is called
with a key-value pair of filters
```js
let updateFilters = filters => {
    console.log(filter.search);
    console.log(filter.role);
}
```

## UploadFile component
UploadFile is a simple file selector component 

### Usage
```vue
<upload-file :is-picture="true"
             :value="user.profilePicture"
             @select-file="input => file = input"
             @remove-file="() => file = null" />
```
* The ```isPicture``` prop passes an accept prop to the input[type=file] field
* The component itself does not upload any files, it is a file selector.
You will have to call a separate mutation to upload the file. For example, in the submit function:
```js
let response = await this.$apollo.mutate({
    mutation: UPLOAD_PICTURE,
    variables: {
        file: this.file,
    },
});
profilePictureId = response.data.uploadPicture.id;
```

### Error validation
v-validate was not working well with file inputs, so one way to validate is to simply have a separate validation
rule
```js
// Before calling the mutation above
if (this.file === null) {
    this.fileHasError = true;
}
```
Then an error prop can be passed to upload-file for showing the error, like:
```vue
<upload-file :is-picture="true"
             :error="fileHasError ? 'This field is required' : ''"
             :value="user.profilePicture"
             @select-file="input => file = input"
             @remove-file="() => file = null" />
```

## Autocomplete component
The autocomplete component uses vue-autosuggest and can be hooked with any data source since the component itself
does not do any GraphQL queries. See UserAutocomplete or CompanyAutocomplete for example of how to combine
this component with GraphQL queries. 

### Usage
```vue
<autocomplete :initial-query="initialQuery"
              :suggestions="suggestions"
              :get-suggestion-value="suggestion => suggestion.item.name"
              :render-suggestion="suggestion => renderSuggestion(suggestion)"
              :loading="loading"
              :class-list="classList"
              @changeLoading="changeLoading"
              @input="query => queryResults(query)"
              @selected="suggestionId => updateValue(suggestionId)" />
```
* `initialQuery` The initial value of the the autosuggest input text, does not query with this value
* `suggestions` The suggestions to show to the user, this can be an array of values or objects (see arguments below)
* `getSuggestionValue` Once a suggestion is selected, the value which will appear in the input text
* `renderSuggestion` Callback which gets passed the selected value or object, should return HTML which will appear in
   the dropdown suggestions.
* `loading` Whether to show loading indicator or not
* `classList` Any additional classes to pass to the input props

Events:
* `@changeLoading` vue-autosuggest changes the value of its "loading" internal parameter which the user clicks outside the
   autosuggest, this event is then fired so that the parent component can change the autocomplete value
* `@input` Called when the user types something into the autosuggest input, should update the suggestions array
   based on the GraphQL / AJAX response
* `@selected` Called when the user selects a suggestion, it is passed with suggestion.id (to keep compliance with vee-validate)

### Company / User Autocomplete
These components are built on top of the autocomplete base component and use GraphQL to query companies or users

```vue
<user-autocomplete :initial-query="user.coach.firstName + ' ' + user.coach.lastName"
                   v-model="user.coachId"
                   v-validate="'required'"
                   name="coach"
                   :class-list="{'is-invalid': submitted && errors.has('coach')}" />
```
* `initialQuery` Same as base component
* `v-model` binds both, @input event and :value parameter, hence whenever a user selects a suggestion, it
   automatically updates the bound parameter with the value
* `v-validate` Vee validate directive, can be used with it like any other input field
* `name` required only for vee-validate to work properly
* `classList` Any additional classes to pass to the input prop
* `queryCoaches` (optional, boolean, only for UserAutocomplete) Whether to query only coaches or all users
* `emptyQueryAfterSelection` (optional, boolean, only for UserAutocomplete) Whether to empty the input text box after
  a suggestion is selected. 