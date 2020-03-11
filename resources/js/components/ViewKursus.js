import React, { Component } from 'react';
import { Button, Table } from 'react-bootstrap';

// import { Container } from './styles';

export default class components extends Component {
  static defaultProps = {
    viewOnly: false
  }

  constructor(props){
    super(props)

    this.state = {
      showFormAdd: false,
      submiting: false,
      id_personal: this.props.id_personal,
      isUpdate: false,
      delete: false
    }

  }

  render() {
    return(
      <div>
        <Table bordered>
          <tbody>
            <tr>
              <th>Nama Kursus</th>
              <th>Penyelenggara</th>
              <th>No Sertifikat</th>
              <th>Tahun</th>
              <th>Provinsi</th>
              {!this.props.viewOnly && (
                <th colSpan={2}>Action</th>
              )}
            </tr>
            {this.props.data.map((d) => (
              <tr>
                <td>{d.Nama_Kursus}</td>
                <td>{d.Nama_Penyelenggara_Kursus}</td>
                <td>{d.No_Sertifikat}</td>
                <td>{d.Tahun}</td>
                <td>{d.ID_Propinsi}</td>
                {!this.props.viewOnly && (<td><Button variant="outline-warning" size="sm" onClick={() => this.props.onUpdateClick(d)}><span className="cui-pencil"></span> Ubah</Button></td>)}
                {!this.props.viewOnly && (<td><Button variant="outline-danger" size="sm" onClick={() => this.props.onDeleteClick(d)}><span className="cui-trash"></span> Delete</Button></td>)}
              </tr>
            ))}
          </tbody>
        </Table>
      </div>
    )
  }
}
