import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios'
import moment from 'moment'
import DataTable from 'react-data-table-component';

const columns = [
  {
    name: '#',
    sortable: true,
    selector: 'index',
    width: "70px"
  },
  {
    name: 'Name',
    selector: 'name',
    sortable: true,
  },
  {
    name: 'Username',
    selector: 'username',
    sortable: true,
  },
  {
    name: 'Role',
    selector: 'role.name',
    sortable: true,
  },
  {
    name: 'Last Login',
    sortable: true,
    cell: row => <span>{moment(row.last_login).format("DD MMM YYYY, HH:mm:ss")}</span>
  },
  {
    name: 'Setting',
    selector: 'username',
    sortable: false,
    right: true,
    cell: row => <span>
    <button class="btn btn-warning btn-sm mr-1"><span class="cui-pencil"></span></button> 
    <button class="btn btn-danger btn-sm"><span class="cui-trash"></span></button>
    </span>
  },
];

export default class User extends Component {
    constructor(props){
      super(props);

      this.state = {
        data: [],
        loading: true
      }
    }

    componentDidMount(){
      axios.get(`/api/users`).then(response => {
        console.log(response)

        let data = []

        response.data.map((obj, i) => {
          obj.index = i + 1
          data.push(obj)
        })

        this.setState({
          data: data,
          loading: false
        })
      }).catch(err => {
        console.log(err)
      })
    }

    render() {
        return (
          <div>
            <div>
                <a href="{{url('users/create')}}" className="btn btn-success btn-sm"><span>Add User</span></a>
            </div>
            <div>
                <DataTable
                  style={{minHeight: 200}}
                  noHeader={true}
                  className="table"
                  columns={columns}
                  striped={true}
                  highlightOnHover={true}
                  data={this.state.data}
                  progressPending={this.state.loading}
                  pagination={true}
                  // paginationServer={true}
                  paginationPerPage={10}
                  progressComponent={
                  <div className="spinner-border text-info mt-5" role="status">
                    <span className="sr-only">Loading...</span>
                  </div>
                  }
                  progressCentered={true}
                />
            </div>
          </div>
        );
    }
}

if (document.getElementById('user')) {
    ReactDOM.render(<User />, document.getElementById('user'));
}
