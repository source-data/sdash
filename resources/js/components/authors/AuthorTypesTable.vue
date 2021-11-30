<template>
  <div class="sd-author-types-table-wrapper">
    <p class="sd-author-types-table-note">Note that adding an author assigns <strong>certain permissions</strong> to the user.</p>
    <div class="sd-author-types-table">
      <button
        class="sd-author-types-table--close-button"
        @click="handleCloseEvent"
      >×</button>
      <table class="sd-author-types-table--table">
        <tr>
          <th>Role</th>
          <th>View</th>
          <th>Edit</th>
          <th>On Author List</th>
        </tr>
        <tr>
          <td>Author</td>
          <td>&check;</td>
          <td>&check;</td>
          <td>&check;</td>
        </tr>
        <tr>
          <td>Corresponding Author</td>
          <td>&check;</td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td>Curator</td>
          <td>&check;</td>
          <td>&check;</td>
          <td></td>
        </tr>
      </table>

    </div>
  </div>
</template>

<script>

export default {

    name: 'AuthorTypesTable',
    methods:{ //run as event handlers, for example

        handleCloseEvent(){
            this.$emit('close');
        }

    }

}
</script>

<style lang="scss">
  .sd-author-types-table-wrapper {
    font-size: 0.687rem;
  }

  .sd-author-types-table-note {
      text-align: right;
      margin: 6px 0;
      padding-right: 1.5rem;
  }

  .sd-author-types-table {
    position: relative;
    display:flex;
    justify-content: flex-end;
    padding-right: 1.5rem;

    border-collapse:collapse;

    th, td {
      padding: 1px 4px;
      border: solid 1px #fff;
      min-width: 6rem;
      text-align: center;
    }
    th:nth-child(1), td:nth-child(1) {
      text-align:left;
    }
  }

  .sd-author-types-table--close-button {
    font-size: 1rem;
    width: 1rem;
    height: 1rem;
    line-height: 1rem;
    font-weight: bold;
    background-color: transparent;
    padding: 0;
    margin: 0;
    border: none;
    color: #fff;
    position: absolute;
    top: -4px;
    right: 0;
  }
</style>