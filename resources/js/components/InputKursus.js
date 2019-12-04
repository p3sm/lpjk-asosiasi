import React, { Component } from 'react';
import { Form, Button, Row, Col, Card, Modal, Table } from 'react-bootstrap';
import Datetime from 'react-datetime'
import MSelectCountry from './MSelectCountry'
import MSelectProvinsi from './MSelectProvinsi'
import MSelectKabupaten from './MSelectKabupaten'
import axios from 'axios'
import Alert from 'react-s-alert';

// import { Container } from './styles';

export default class components extends Component {
  constructor(props){
    super(props)

    this.state = {
      showFormAdd: false,
      submiting: false,
      id_personal: this.props.id_personal,
      isUpdate: false
    }

  }

  componentDidMount(){
  }

  handleClose = () => {
    this.setState({showFormAdd: false})
  }

  onProvinsiChange = (data) => {
    this.setState({provinsi: data.value})
    this.selectKabupaten.getKabupaten(data.value)
  }

  onNegaraChange = (data) => {
    console.log(data)
    this.setState({negara: data.value})
  }

  openUpdateForm = (data) => {
    this.setState({
      showFormAdd: true,
      isUpdate: true,
      ID_Personal_Kursus: data.ID_Personal_Kursus,
      id_personal: data.ID_Personal,
      nama_kursus: data.Nama_Kursus,
      penyelenggara: data.Nama_Penyelenggara_Kursus,
      alamat: data.Alamat1,
      provinsi: data.ID_Propinsi,
      kabupaten: data.ID_Kabupaten,
      negara: data.ID_Countries,
      tahun: data.Tahun,
      no_sertifikat: data.No_Sertifikat,
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

    switch(event.target.id){
      case "file_persyaratan":
        label.prepend("Upload Persyaratan Kursus ")
        this.setState({ file_persyaratan: event.target.files[0] })
        break;
      default:
        break;
    }
  }

  handleSubmit = () => {
    this.setState({submiting: true})

    var formData = new FormData();
    formData.append("ID_Personal_Kursus", this.state.ID_Personal_Kursus);
    formData.append("id_personal", this.state.id_personal);
    formData.append("nama_kursus", this.state.nama_kursus);
    formData.append("penyelenggara", this.state.penyelenggara);
    formData.append("alamat", this.state.alamat);
    formData.append("provinsi", this.state.provinsi);
    formData.append("kabupaten", this.state.kabupaten);
    formData.append("negara", this.state.negara);
    formData.append("tahun", this.state.tahun);
    formData.append("no_sertifikat", this.state.no_sertifikat);
    formData.append("file_persyaratan", this.state.file_persyaratan);
    var uri = this.state.isUpdate ? "/api/kursus/update" : "/api/kursus/create"

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

  resetState = () => {
    this.setState({
      nama_kursus: "",
      penyelenggara: "",
      alamat: "",
      provinsi: "",
      kabupaten: "",
      negara: "",
      tahun: "",
      no_sertifikat: "",
      file_persyaratan: ""
    })
  }

  render() {
    return(
      <div>
        <Button variant="outline-info" className="mb-3" onClick={() => this.setState({showFormAdd: true})}><span className="fa fa-edit"></span>Tambah Data</Button>
        <Table bordered>
          <tbody>
            <tr>
              <th>Nama Kursus</th>
              <th>Penyelenggara</th>
              <th>No Sertifikat</th>
              <th>Tahun</th>
              <th>Provinsi</th>
              <th>Action</th>
            </tr>
            {this.props.data.map((d) => (
              <tr>
                <td>{d.Nama_Kursus}</td>
                <td>{d.Nama_Penyelenggara_Kursus}</td>
                <td>{d.No_Sertifikat}</td>
                <td>{d.Tahun}</td>
                <td>{d.ID_Propinsi}</td>
                <td><Button variant="outline-warning" size="sm" onClick={() => this.openUpdateForm(d)}><span className="cui-pencil"></span> Ubah</Button></td>
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
                    <Form.Label>Nama Penyelenggara</Form.Label>
                    <Form.Control placeholder="" onChange={(e) => this.setState({penyelenggara: e.target.value})} value={this.state.penyelenggara}></Form.Control>
                  </Form.Group>

                  <MSelectCountry value={this.state.negara} onChange={(data) => this.onNegaraChange(data)} />

                  <MSelectProvinsi value={this.state.provinsi} onChange={(data) => this.onProvinsiChange(data)} />

                  <MSelectKabupaten value={this.state.kabupaten} provinsiId={this.state.provinsi} onRef={ref => (this.selectKabupaten = ref)} onChange={(data) => this.setState({kabupaten: data.value})} />

                  <Form.Group>
                    <Form.Label>Alamat</Form.Label>
                    <Form.Control as="textarea" row="3" onChange={(e) => this.setState({alamat: e.target.value})} value={this.state.alamat}></Form.Control>
                  </Form.Group>
                </Col>
                <Col md>
                  <Form.Group>
                    <Form.Label>Nama Kursus</Form.Label>
                    <Form.Control placeholder="" onChange={(e) => this.setState({nama_kursus: e.target.value})} value={this.state.nama_kursus}></Form.Control>
                  </Form.Group>
                  <Form.Group>
                    <Form.Label>No. Sertifikat</Form.Label>
                    <Form.Control type="text" placeholder="" onChange={(e) => this.setState({no_sertifikat: e.target.value})} value={this.state.no_sertifikat}></Form.Control>
                  </Form.Group>
                  <Form.Group>
                    <Form.Label>Tahun</Form.Label>
                    <Form.Control type="email" placeholder="" onChange={(e) => this.setState({tahun: e.target.value})} value={this.state.tahun}></Form.Control>
                  </Form.Group>
                  <div class="custom-file mb-3">
                    <input type="file" class="custom-file-input" id="file_persyaratan" onChange={this.onUploadChangeHandler}></input>
                    <label class="custom-file-label" for="file_persyaratan">Upload Persyaratan Kursus</label>
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
          <Alert stack={{limit: 3}} position="top-right" offset="40" effect="slide" timeout="none" />
        </Modal>
      </div>
    )
  }
}
