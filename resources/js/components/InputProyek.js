import React, { Component } from 'react';
import { Form, Button, Row, Col, Card, Modal, Table } from 'react-bootstrap';
import Datetime from 'react-datetime'
import MSelectProvinsi from './MSelectProvinsi'
import axios from 'axios'
import Alert from 'react-s-alert';
import SweetAlert from 'react-bootstrap-sweetalert';

// import { Container } from './styles';


const formatter = new Intl.NumberFormat("id-ID", {
  style: "decimal",
  currency: "IDR"
});

export default class components extends Component {
  constructor(props){
    super(props)

    this.state = {
      showFormAdd: false,
      submiting: false,
      id_personal: this.props.id_personal,
      isUpdate: false,
      nilai_proyek: 0,
      delete: false
    }

  }

  componentDidMount(){
  }

  handleClose = () => {
    this.setState({showFormAdd: false})
  }

  openUpdateForm = (data) => {
    this.setState({
      showFormAdd: true,
      isUpdate: true,
      id_personal_proyek: data.id_personal_proyek,
      id_personal: data.id_personal,
      nama_proyek: data.Proyek,
      lokasi: data.Lokasi,
      tgl_mulai: data.Tgl_Mulai,
      tgl_selesai: data.Tgl_Selesai,
      jabatan: data.Jabatan,
      nilai_proyek: data.Nilai,
    })
  }

  onUploadChangeHandler = event => {
    var size = event.target.files[0].size
    var label = $( event.target ).siblings("label")

    if(size > 20000000){
      Alert.error('Max file size 20mb')

      return
    }

    label.addClass("selected")
    label.html(event.target.files[0].name)
    label.css("border", "#6ab04c solid 1px")
    label.css("background", "#f0f3f1")
    
    var check = '<i class="fa fa-check" aria-hidden="true" style="color: #6cae64;margin-right: 10px;"></i>';

    switch(event.target.id){
      case "file_pengalaman":
        label.prepend(check + "Upload Pengalaman Proyek ")
        this.setState({ file_pengalaman: event.target.files[0] })
        break;
      default:
        break;
    }
  }

  handleSubmit = () => {
    this.setState({submiting: true})

    var formData = new FormData();
    formData.append("id_personal_proyek", this.state.id_personal_proyek);
    formData.append("id_personal", this.state.id_personal);
    formData.append("nama_proyek", this.state.nama_proyek);
    formData.append("lokasi", this.state.lokasi);
    formData.append("tgl_mulai", this.state.tgl_mulai);
    formData.append("tgl_selesai", this.state.tgl_selesai);
    formData.append("jabatan", this.state.jabatan);
    formData.append("nilai_proyek", this.state.nilai_proyek);
    formData.append("file_pengalaman", this.state.file_pengalaman);

    var uri = this.state.isUpdate ? "/api/proyek/update" : "/api/proyek/create"

    axios.post(uri, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    }).then(response => {
      console.log(response)
      
      this.setState({submiting: false, showFormAdd: false})
      this.resetState()
      this.props.refreshData()

      Alert.success(response.data.message);
      
    }).catch(err => {
      console.log(err.response.data.message)

      this.setState({submiting: false})
      Alert.error(err.response.data.message);
    })
  }

  confirmDelete = (id) => {
    console.log(id)
    this.setState({delete: true, deleteId: id})
  }

  deleteProyek = (id) => {
    this.setState({deleting: true})

    var formData = new FormData();
    formData.append("id_personal_proyek", id);
      
    axios.post("/api/proyek/delete", formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    }).then(response => {
      console.log(response)
      
      this.setState({deleting: false, delete: false})
      this.props.refreshData()
      
      Alert.success(response.data.message);
      
    }).catch(err => {
      console.log(err.response.data.message)

      this.setState({deleting: false, delete: false})
      Alert.error(err.response.data.message);
    })
  }


  resetState = () => {
    this.setState({
      nama_proyek: "",
      lokasi: "",
      tgl_mulai: "",
      tgl_selesai: "",
      jabatan: "",
      nilai_proyek: "",
      file_pengalaman: ""
    })
  }

  render() {
    return(
      <div>
        <Button variant="outline-info" className="mb-3" onClick={() => this.setState({showFormAdd: true})}><span className="fa fa-edit"></span>Tambah Data</Button>
        <Table bordered>
          <tbody>
            <tr>
              <th>Nama Proyek</th>
              <th>Jabatan</th>
              <th>Nilai</th>
              <th>Tanggal</th>
              <th>Lokasi</th>
              <th colSpan={2}>Action</th>
            </tr>
            {this.props.data.map((d) => (
              <tr>
                <td>{d.Proyek}</td>
                <td>{d.Jabatan}</td>
                <td>{formatter.format(d.Nilai)}</td>
                <td>{d.Tgl_Mulai} - {d.Tgl_Selesai}</td>
                <td>{d.Lokasi}</td>
                <td><Button variant="outline-warning" size="sm" onClick={() => this.openUpdateForm(d)}><span className="cui-pencil"></span> Ubah</Button></td>
                <td><Button variant="outline-danger" size="sm" onClick={() => this.confirmDelete(d.id_personal_proyek)}><span className="cui-trash"></span> Delete</Button></td>
              </tr>
            ))}
          </tbody>
        </Table>
        <Modal
        size="xl"
        onHide={this.handleClose}
        show={this.state.showFormAdd}>
          <Modal.Header closeButton>
            <Modal.Title>{this.state.isUpdate ? "Ubah" : "Tambah"} Data</Modal.Title>
          </Modal.Header>
          <Modal.Body>
            <Form>
              <Row>
                <Col>
                  <Form.Group>
                    <Form.Label>Proyek</Form.Label>
                    <Form.Control placeholder="" onChange={(e) => this.setState({nama_proyek: e.target.value})} value={this.state.nama_proyek}></Form.Control>
                  </Form.Group>

                  <MSelectProvinsi value={this.state.lokasi} onChange={(data) => this.setState({lokasi: data.value})} />

                  <Form.Group>
                    <Form.Label>Tanggal Awal</Form.Label>
                    <Datetime closeOnSelect={true} inputProps={{ placeholder: 'contoh: 1980-01-01'}} value={this.state.tgl_mulai} dateFormat="YYYY-MM-DD" onChange={(e) => {
                      try {
                        this.setState({tgl_mulai: e.format("YYYY-MM-DD")})
                      } catch (err) {
                        this.setState({tgl_mulai: e})
                      }
                    }} timeFormat={false} />
                  </Form.Group>
                  <Form.Group>
                    <Form.Label>Tanggal Akhir</Form.Label>
                    <Datetime closeOnSelect={true} inputProps={{ placeholder: 'contoh: 1980-01-01'}} value={this.state.tgl_selesai} dateFormat="YYYY-MM-DD" onChange={(e) => {
                      try {
                        this.setState({tgl_selesai: e.format("YYYY-MM-DD")})
                      } catch (err) {
                        this.setState({tgl_selesai: e})
                      }
                    }} timeFormat={false} />
                  </Form.Group>
                </Col>
                <Col md>
                  <Form.Group>
                    <Form.Label>Jabatan</Form.Label>
                    <Form.Control placeholder="" onChange={(e) => this.setState({jabatan: e.target.value})} value={this.state.jabatan}></Form.Control>
                  </Form.Group>
                  <Form.Group>
                    <Form.Label>Nilai Proyek</Form.Label>
                    <Form.Control placeholder="" onChange={(e) =>  this.setState({nilai_proyek: e.target.value.split('.').join("")})} value={formatter.format(this.state.nilai_proyek)}></Form.Control>
                  </Form.Group>
                  <div class="custom-file mb-3">
                    <input type="file" class="custom-file-input" id="file_pengalaman" onChange={this.onUploadChangeHandler}></input>
                    <label class="custom-file-label" for="file_pengalaman">Upload Pengalaman Proyek</label>
                  </div>
                </Col>
              </Row>
            </Form>
          </Modal.Body>
          <Modal.Footer>
            <Button variant="light" onClick={this.handleClose}>
              Cancel
            </Button>
            <Button className="d-flex" disabled={this.state.submiting} variant="primary" onClick={!this.state.submiting ? this.handleSubmit : null}>
              {this.state.submiting ? 'Submiting...' : 'Submit'}
            </Button>
          </Modal.Footer>
          <Alert stack={{limit: 3}} position="top-right" offset="40" effect="slide" timeout="2000" />
        </Modal>
          
          <SweetAlert
            show={this.state.delete}
            warning
            showCancel
            title="Hapus Data"
            btnSize="md"
            confirmBtnBsStyle='success'
            cancelBtnText="Close"
            confirmBtnText={this.state.deleting ? "Deleting..." : "Delete"}
            onConfirm={() => this.deleteProyek(this.state.deleteId)}
            onCancel={() => this.setState({delete: false})}
          >Anda yakin akan menghapus data ini?</SweetAlert>
      </div>
    )
  }
}
