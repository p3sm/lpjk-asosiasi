import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import { Tabs, Tab, Form, Spinner, Col, Row, Modal} from 'react-bootstrap';
import axios from 'axios'
import moment from 'moment'
import SweetAlert from 'react-bootstrap-sweetalert';
import Alert from 'react-s-alert';

import InputBiodata from './InputBiodata'
import InputNewBiodata from './InputNewBiodata'
import InputPendidikan from './InputPendidikan'
import InputKursus from './InputKursus'
import InputOrganisasi from './InputOrganisasi'
import InputProyek from './InputProyek'
import InputKualifikasi from './InputKualifikasi'

export default class Personal extends Component {
    constructor(props){
      super(props);

      this.state = {
        data: [],
        loading: false,
        id_personal: "",
        biodata: null,
        pendidikan: [],
        kursus: [],
        organisasi: [],
        proyek: [],
        kualifikasi_ta: [],
        error: false,
        showFormAddBiodata: false
      }
    }

    componentDidMount(){
    }

    onNikSearch(e){
      e.preventDefault()

      if(!this.state.loading){
        if(this.state.id_personal == ""){
          Alert.error('ID Personal / KTP tidak boleh kosong', {
            position: 'top-right',
            offset: 50,
            effect: 'slide',
            timeout: 'none'
          });

          return
        }

        this.getBiodata()
      }
    }

    getBiodata(){
      this.setState({loading: true, biodata: null})

      let body = {id_personal: this.state.id_personal}
  
      axios.post(`/api/biodata`, body).then(response => {
        let result = response.data
        console.log(result.data)

        this.setState({
          biodata: result.data,
          loading: false
        })

        this.getPendidikan()
        this.getKursus()
        this.getOrganisasi()
        this.getProyek()
        this.getKualifikasi()
        
      }).catch(err => {
        console.log(err.response)

        this.setState({
          loading: false,
          error: true,
          errorMsg: err.response.data.message
        })
      })
    }

    getPendidikan(){
      this.setState({pendidikan: []})

      let body = {id_personal: this.state.id_personal}
  
      axios.get(`/api/pendidikan/` + this.state.id_personal).then(response => {
        let result = response.data
        console.log(result.data)

        this.setState({
          pendidikan: result.data,
          loading: false
        })
      }).catch(err => {
        console.log(err.response)

        this.setState({
          loading: false,
          // error: true,
          errorMsg: err.response.data.message
        })
      })
    }

    getKursus(){
      this.setState({kursus: []})

      let body = {id_personal: this.state.id_personal}
  
      axios.post(`/api/kursus`, body).then(response => {
        let result = response.data
        console.log(result.data)

        this.setState({
          kursus: result.data,
          loading: false
        })
      }).catch(err => {
        console.log(err.response)

        this.setState({
          loading: false,
          // error: true,
          errorMsg: err.response.data.message
        })
      })
    }

    getOrganisasi(){
      this.setState({organisasi: []})

      let body = {id_personal: this.state.id_personal}
  
      axios.post(`/api/organisasi`, body).then(response => {
        let result = response.data
        console.log(result.data)

        this.setState({
          organisasi: result.data,
          loading: false
        })
      }).catch(err => {
        console.log(err.response)

        this.setState({
          loading: false,
          // error: true,
          errorMsg: err.response.data.message
        })
      })
    }

    getProyek(){
      this.setState({proyek: []})

      let body = {id_personal: this.state.id_personal}
  
      axios.post(`/api/proyek`, body).then(response => {
        let result = response.data
        console.log(result.data)

        this.setState({
          proyek: result.data,
          loading: false
        })
      }).catch(err => {
        console.log(err.response)

        this.setState({
          loading: false,
          // error: true,
          errorMsg: err.response.data.message
        })
      })
    }

    getKualifikasi(){
      this.setState({kualifikasi_ta: []})

      let body = {id_personal: this.state.id_personal}
  
      axios.post(`/api/kualifikasi_ta`, body).then(response => {
        let result = response.data
        console.log(result.data)

        this.setState({
          kualifikasi_ta: result.data,
          loading: false
        })
      }).catch(err => {
        console.log(err.response)

        this.setState({
          loading: false,
          // error: true,
          errorMsg: err.response.data.message
        })
      })
    }

    handleChange(event){
      this.setState({
        id_personal: event.target.value
      })
    }

    render() {
        return (
          <div>
            <Form onSubmit={(e) => this.onNikSearch(e)}>
              <div className="form-group">
                <label htmlFor="nik">ID Personal / KTP</label>
                <div className="input-group">
                  <Form.Control id="nama" placeholder="No. KTP" onChange={(event) => this.handleChange(event)} value={this.state.id_personal}></Form.Control>
                  <div className="input-group-append">
                    <button className={"btn btn-" + (this.state.loading ? "light" : "primary")} onClick={(e) => this.onNikSearch(e)} type="button">{this.state.loading ? "Mencari..." : "Cari Data"}</button>
                  </div>
                </div>
              </div>
              <Row style={{justifyContent: "center", display: this.state.loading ? "flex" : "none"}}>
                <Spinner style={{alignSelf: "center"}} animation="border" variant="primary" />
              </Row>
              
              <InputNewBiodata tipe_profesi={1} id_personal={this.state.id_personal} visible={this.state.showFormAddBiodata} onClose={() => this.setState({showFormAddBiodata: false})} onSuccess={() => this.setState({showFormAddBiodata: false}, () => this.getBiodata())} />
              
              <SweetAlert
                show={this.state.error}
                danger
                showCancel
                title="Maaf"
                btnSize="sm"
                confirmBtnBsStyle='success'
                cancelBtnText="Close"
                confirmBtnText="Buat data baru"
                onConfirm={() => this.setState({error: false, showFormAddBiodata: true})}
                onCancel={() => this.setState({error: false})}
              >{this.state.errorMsg}</SweetAlert>
              {this.state.biodata != null && (
                <Tabs defaultActiveKey="biodata" id="">
                  <Tab eventKey="biodata" title="Biodata">
                    <InputBiodata tipe_profesi={1} data={this.state.biodata} refreshData={() => this.getBiodata()}/>
                  </Tab>
                  <Tab eventKey="pendidikan" title="Pendidikan">
                    <InputPendidikan id_personal={this.state.id_personal} data={this.state.pendidikan} refreshData={() => this.getPendidikan()} />
                  </Tab>
                  <Tab eventKey="kursus" title="Kursus">
                    <InputKursus id_personal={this.state.id_personal} data={this.state.kursus} refreshData={() => this.getPendidikan()}/>
                  </Tab>
                  <Tab eventKey="organisasi" title="Pengalaman Organisasi">
                    <InputOrganisasi id_personal={this.state.id_personal} data={this.state.organisasi} refreshData={() => this.getPendidikan()}/>
                  </Tab>
                  <Tab eventKey="proyek" title="Pengalaman Proyek">
                    <InputProyek id_personal={this.state.id_personal} data={this.state.proyek} refreshData={() => this.getPendidikan()}/>
                  </Tab>
                  <Tab eventKey="kualifikasi" title="Klasifikasi Kualifikasi">
                    <InputKualifikasi tipe_profesi={1} id_personal={this.state.id_personal} data={this.state.kualifikasi_ta} refreshData={() => this.getPendidikan()}/>
                  </Tab>
                </Tabs>
              )}
            </Form>
            <Alert stack={{limit: 3}} />
          </div>
        );
    }
}

if (document.getElementById('permohonan_ska')) {
    ReactDOM.render(<Personal />, document.getElementById('permohonan_ska'));
}
