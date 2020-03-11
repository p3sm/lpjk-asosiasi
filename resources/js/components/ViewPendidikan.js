import React, { Component } from 'react';
import { Button, Table } from 'react-bootstrap';

export default class components extends Component {
  static defaultProps = {
    viewOnly: false
  }

  constructor(props){
    super(props)

    this.state = {
      showFormAdd: false,
      submiting: false,
      isUpdate: false,
      file_data_pendidikan: "",
      file_keterangan_sekolah: "",
      delete: false,
      negara: "ID",
    }
  }

  componentDidMount(){
  }

  render() {
    return(
      <div>
        <Table bordered>
          <tbody>
            <tr>
              <th>Nama Sekolah</th>
              <th>Program Studi</th>
              <th>No Ijazah</th>
              <th>Tahun</th>
              <th>Provinsi</th>
              <th>Alamat</th>
              {!this.props.viewOnly && (
                <th colSpan={2}>Action</th>
              )}
            </tr>
            {this.props.data.map((d) => (
              <tr>
                <td>{d.Nama_Sekolah}</td>
                <td>{d.Jurusan}</td>
                <td>{d.No_Ijazah}</td>
                <td>{d.Tahun}</td>
                <td>{d.ID_Propinsi}</td>
                <td>{d.Alamat1}</td>
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
